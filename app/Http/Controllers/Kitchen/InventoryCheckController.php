<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ingredient;
use App\Models\Inventory;
use App\Models\InventoryCheck;
use App\Models\InventoryCheckItem;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class InventoryCheckController extends Controller
{
    /**
     * Display a listing of ingredients for inventory check.
     * Kitchen team is responsible for counting inventory and reporting to cook/admin.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get existing inventory items for reference (only items previously reported by kitchen)
        // NO HARDCODED DATA - only shows items that have been reported before
        $existingItems = Inventory::orderBy('name')->get();

        // Get recent inventory checks for history (user's own reports only)
        $recentChecks = InventoryCheck::with(['user', 'items'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get all inventory checks for history section (paginated)
        $allChecks = InventoryCheck::with(['user', 'items'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get statistics for the current user
        $stats = [
            'total_reports' => InventoryCheck::where('user_id', Auth::id())->count(),
            'total_items_reported' => InventoryCheck::where('user_id', Auth::id())
                ->withCount('items')
                ->get()
                ->sum('items_count'),
            'last_report_date' => InventoryCheck::where('user_id', Auth::id())
                ->latest()
                ->value('created_at'),
        ];

        return view('kitchen.inventory.index', compact('existingItems', 'recentChecks', 'allChecks', 'stats'));
    }
    
    /**
     * Submit an inventory check from kitchen team to cook/admin.
     * This allows kitchen staff to report actual inventory levels.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Prevent duplicate submissions within 30 seconds
        $recentCheck = InventoryCheck::where('user_id', Auth::id())
            ->where('created_at', '>=', now()->subSeconds(30))
            ->first();

        if ($recentCheck) {
            return redirect()->back()
                ->with('error', 'Please wait before submitting another inventory check.')
                ->withInput();
        }

        // Validate the manual inventory input
        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|string',
            'manual_items' => 'required|array',
            'manual_items.*.name' => 'required|string|max:255',
            'manual_items.*.quantity' => 'required|numeric|min:0',
            'manual_items.*.unit' => 'required|string|max:50',
            'manual_items.*.needs_restock' => 'nullable',
            'manual_items.*.notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create inventory check record submitted by kitchen staff
        $check = new InventoryCheck();
        $check->user_id = Auth::id();
        $check->check_date = now();
        $check->notes = $request->notes;
        $check->save();
        
        // Create a notification for the cook
        try {
            // Only create notification if the Notification model exists
            if (class_exists('\App\Models\Notification')) {
                // Find cook/admin users to notify
                $cookUsers = \App\Models\User::where('role', 'cook')->get();

                foreach ($cookUsers as $user) {
                    \App\Models\Notification::create([
                        'user_id' => $user->id,
                        'title' => 'New Inventory Check',
                        'message' => 'Kitchen staff has submitted a new inventory count.',
                        'type' => 'inventory_check',
                        'data' => [
                            'inventory_check_id' => $check->id,
                            'action_url' => route('cook.inventory.show-report', $check->id)
                        ],
                        'read_at' => null
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Continue even if notification fails
        }
        
        // Process manually entered inventory items (NO HARDCODED DATA)
        // Kitchen staff reports actual physical counts - this creates the baseline data
        if ($request->has('manual_items')) {
            foreach ($request->manual_items as $item) {
                // Find or create ingredient first (for the foreign key relationship)
                $ingredient = \App\Models\Ingredient::firstOrCreate(
                    ['name' => $item['name']],
                    [
                        'name' => $item['name'],
                        'unit' => $item['unit'],
                        'category' => 'general',
                        'description' => 'Added from kitchen inventory check',
                        'current_stock' => $item['quantity'],
                        'minimum_stock' => 10
                    ]
                );

                // Find or create inventory item based on kitchen's actual count
                // This is the ONLY way inventory items are created - from kitchen reports
                $inventoryItem = Inventory::firstOrCreate(
                    ['name' => $item['name']],
                    [
                        'name' => $item['name'],
                        'description' => 'Added from kitchen inventory check',
                        'quantity' => $item['quantity'],
                        'unit' => $item['unit'],
                        'category' => 'general', // Default category, cook can adjust later
                        'reorder_point' => 10, // Default reorder point, cook can adjust later
                        'last_updated_by' => Auth::id(),
                        'status' => 'available'
                    ]
                );

                // Update existing inventory item
                $previousQuantity = $inventoryItem->quantity;
                $inventoryItem->update([
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'],
                    'last_updated_by' => Auth::id(),
                    'status' => $this->determineStatus($item['quantity'], $inventoryItem->reorder_point)
                ]);

                // Update ingredient stock as well
                $ingredient->update([
                    'current_stock' => $item['quantity'],
                    'unit' => $item['unit']
                ]);

                // Create check item for each manually entered item (using ingredient_id)
                $checkItem = new InventoryCheckItem();
                $checkItem->inventory_check_id = $check->id;
                $checkItem->ingredient_id = $ingredient->id; // Use ingredient ID, not inventory ID
                $checkItem->current_stock = $item['quantity'];
                $checkItem->needs_restock = isset($item['needs_restock']) ? true : false;
                $checkItem->notes = $item['notes'] ?? null;
                $checkItem->save();

                // Create inventory history record (only if inventory item exists)
                if (class_exists('\App\Models\InventoryHistory') && $inventoryItem && $inventoryItem->id) {
                    try {
                        \App\Models\InventoryHistory::create([
                            'inventory_item_id' => $inventoryItem->id,
                            'user_id' => Auth::id(),
                            'action_type' => 'report',
                            'quantity_change' => $item['quantity'] - $previousQuantity,
                            'previous_quantity' => $previousQuantity,
                            'new_quantity' => $item['quantity'],
                            'notes' => "Kitchen inventory check: " . ($item['notes'] ?? 'No notes')
                        ]);
                    } catch (\Exception $e) {
                        // Continue even if history creation fails
                        \Log::warning('Failed to create inventory history: ' . $e->getMessage());
                    }
                }
            }
        }

        // Send notification to cook about inventory update
        $notificationService = new NotificationService();
        $notificationService->inventoryReportSubmitted([
            'id' => $check->id,
            'submitted_by' => Auth::id(),
            'items_count' => count($request->manual_items),
            'restock_needed' => collect($request->manual_items)->where('needs_restock', true)->count()
        ]);

        return redirect()->route('kitchen.inventory')
            ->with('success', 'Inventory check submitted successfully! Cook has been notified.');
    }

    /**
     * Determine inventory status based on quantity and reorder point
     */
    private function determineStatus($quantity, $reorderPoint)
    {
        if ($quantity <= 0) {
            return 'out_of_stock';
        } elseif ($quantity <= $reorderPoint) {
            return 'low_stock';
        }
        return 'available';
    }

    /**
     * Send notification to cook about inventory update
     */
    private function notifyCookAboutInventoryUpdate($inventoryCheck)
    {
        // Get all cook users
        $cooks = User::where('role', 'cook')->get();

        foreach ($cooks as $cook) {
            // Create notification for each cook
            try {
                if (class_exists('\App\Models\Notification')) {
                    \App\Models\Notification::create([
                        'user_id' => $cook->id,
                        'type' => 'inventory_update',
                        'title' => 'Inventory Report Received',
                        'message' => 'Kitchen team has submitted an inventory check report. Please review the stock levels.',
                        'data' => [
                            'inventory_check_id' => $inventoryCheck->id,
                            'submitted_by' => Auth::user()->name,
                            'submitted_at' => now()->format('Y-m-d H:i:s'),
                            'action_url' => route('cook.inventory.show-report', $inventoryCheck->id)
                        ],
                        'read_at' => null
                    ]);
                }
            } catch (\Exception $e) {
                // Continue even if notification creation fails
            }
        }
    }

    /**
     * Get inventory check history
     */
    public function history()
    {
        $checks = InventoryCheck::with(['user', 'items.ingredient'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('kitchen.inventory.history', compact('checks'));
    }

    /**
     * Show specific inventory check details
     */
    public function show($id)
    {
        $check = InventoryCheck::with(['user', 'items.ingredient'])
            ->findOrFail($id);

        return view('kitchen.inventory.show', compact('check'));
    }

    /**
     * Delete a specific inventory check report
     */
    public function destroy($id)
    {
        try {
            $check = InventoryCheck::findOrFail($id);

            // Only allow deletion by the user who created it or admin
            if ($check->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only delete your own reports.'
                ], 403);
            }

            // Delete related inventory check items first
            $check->items()->delete();

            // Delete the inventory check
            $check->delete();

            return response()->json([
                'success' => true,
                'message' => 'Inventory report deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete all inventory check reports for the current user
     */
    public function destroyAll()
    {
        try {
            $userId = Auth::id();

            // Get all checks for the current user
            $checks = InventoryCheck::where('user_id', $userId)->get();

            if ($checks->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No reports found to delete.'
                ]);
            }

            $deletedCount = 0;

            foreach ($checks as $check) {
                // Delete related inventory check items first
                $check->items()->delete();

                // Delete the inventory check
                $check->delete();
                $deletedCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully deleted {$deletedCount} inventory reports."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting reports: ' . $e->getMessage()
            ], 500);
        }
    }
}
