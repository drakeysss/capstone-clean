<?php

namespace App\Http\Controllers\Cook;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\InventoryCheck;
use App\Models\InventoryCheckItem;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventoryController extends Controller
{
    /**
     * Display the cook's inventory dashboard
     */
    public function index(Request $request)
    {
        // Cook/Admin role: Review kitchen inventory reports and approve restocking decisions

        // Get recent inventory reports from kitchen
        $query = InventoryCheck::with(['user', 'items']);

        // Apply filters
        if ($request->has('status') && $request->status) {
            switch ($request->status) {
                case 'pending':
                    $query->whereNull('approved_at');
                    break;
                case 'approved':
                    $query->whereNotNull('approved_at');
                    break;
                case 'needs_restock':
                    $query->whereHas('items', function($q) {
                        $q->where('needs_restock', true);
                    });
                    break;
            }
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $recentChecks = $query->orderBy('created_at', 'desc')->paginate(15)->appends($request->query());

        // Get statistics from kitchen reports (all dynamic, no hardcoded values)
        $stats = [
            'total_reports' => InventoryCheck::count(),
            'pending_approvals' => InventoryCheck::whereNull('approved_at')->count(),
            'items_needing_restock' => InventoryCheck::whereHas('items', function($q) {
                $q->where('needs_restock', true);
            })->count(),
            'recent_reports' => InventoryCheck::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        return view('cook.inventory.index', compact('recentChecks', 'stats'));
    }

    /**
     * Show inventory reports from kitchen team
     */
    public function reports(Request $request)
    {
        $query = InventoryCheck::with(['user', 'items.ingredient']);

        // Apply date filter
        if ($request->has('date_from') && $request->date_from) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        // Apply kitchen staff filter
        if ($request->has('staff_id') && $request->staff_id) {
            $query->where('user_id', $request->staff_id);
        }

        $reports = $query->orderBy('created_at', 'desc')->paginate(15)->appends($request->query());

        // Get kitchen staff for filter
        $kitchenStaff = User::where('role', 'kitchen')->get();

        return view('cook.inventory.reports', compact('reports', 'kitchenStaff'));
    }

    /**
     * Show detailed inventory report
     */
    public function showReport($id)
    {
        $report = InventoryCheck::with(['user', 'items.ingredient'])
            ->findOrFail($id);

        // Mark notification as read if exists
        try {
            if (class_exists('\App\Models\Notification')) {
                $notification = \App\Models\Notification::where('user_id', Auth::id())
                    ->where('type', 'inventory_update')
                    ->whereJsonContains('data', json_encode(['inventory_check_id' => (int)$id]))
                    ->whereNull('read_at')
                    ->first();

                if ($notification) {
                    $notification->markAsRead();
                }
            }
        } catch (\Exception $e) {
            // Continue even if notification marking fails
        }

        return view('cook.inventory.show-report', compact('report'));
    }

    /**
     * Approve inventory report and restocking decisions
     */
    public function approveReport(Request $request, $id)
    {
        $report = InventoryCheck::findOrFail($id);

        $request->validate([
            'approval_notes' => 'nullable|string|max:500',
            'approved_items' => 'array',
            'approved_items.*' => 'exists:inventory_check_items,id'
        ]);

        // Mark report as approved
        $report->update([
            'approved_at' => now(),
            'approved_by' => Auth::id(),
            'approval_notes' => $request->approval_notes
        ]);

        // Send notification to kitchen staff about approval
        $notificationService = new NotificationService();
        $notificationService->inventoryReportApproved([
            'id' => $report->id,
            'submitted_by' => $report->user_id,
            'approved_by' => Auth::user()->name,
            'approval_notes' => $request->approval_notes
        ]);

        return redirect()->back()->with('success', 'Inventory report approved successfully!');
    }

    /**
     * Record approved restocking/delivery
     */
    public function recordRestock(Request $request)
    {
        $request->validate([
            'report_id' => 'required|exists:inventory_checks,id',
            'item_name' => 'required|string',
            'quantity_restocked' => 'required|numeric|min:0',
            'restock_date' => 'required|date',
            'notes' => 'nullable|string|max:500'
        ]);

        // Find or create inventory item
        $inventoryItem = Inventory::firstOrCreate(
            ['name' => $request->item_name],
            [
                'name' => $request->item_name,
                'description' => 'Created from approved restock',
                'quantity' => 0,
                'unit' => 'units',
                'category' => 'general',
                'reorder_point' => 10,
                'last_updated_by' => Auth::id(),
                'status' => 'available'
            ]
        );

        // Update quantity
        $inventoryItem->quantity += $request->quantity_restocked;
        $inventoryItem->last_updated_by = Auth::id();
        $inventoryItem->save();

        return redirect()->back()->with('success', 'Restock recorded successfully!');
    }

    /**
     * Get inventory alerts
     */
    public function alerts()
    {
        $lowStockItems = Inventory::lowStock()->get();
        $outOfStockItems = Inventory::outOfStock()->get();
        $expiringItems = Inventory::expiringSoon()->get();

        return view('cook.inventory.alerts', compact('lowStockItems', 'outOfStockItems', 'expiringItems'));
    }

    /**
     * Get alerts for API
     */
    public function getAlerts()
    {
        $lowStockItems = Inventory::lowStock()
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->name,
                    'quantity' => $item->quantity,
                    'unit' => $item->unit,
                    'reorder_point' => $item->reorder_point,
                    'status' => 'low'
                ];
            });

        return response()->json([
            'success' => true,
            'requirements' => $lowStockItems
        ]);
    }

    /**
     * Record delivery/restock
     */
    public function notifyDelivery(Request $request)
    {
        $validated = $request->validate([
            'inventory_id' => 'required|exists:inventory,id',
            'delivery_date' => 'required|date',
            'quantity' => 'required|numeric|min:0'
        ]);

        // Update inventory quantity
        $inventory = Inventory::findOrFail($validated['inventory_id']);
        $previousQuantity = $inventory->quantity;
        $inventory->quantity += $validated['quantity'];
        $inventory->last_updated_by = Auth::id();
        $inventory->save();

        // Create delivery record if table exists
        if (DB::getSchemaBuilder()->hasTable('inventory_deliveries')) {
            DB::table('inventory_deliveries')->insert([
                'inventory_id' => $validated['inventory_id'],
                'delivery_date' => $validated['delivery_date'],
                'quantity' => $validated['quantity'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Delivery recorded successfully'
        ]);
    }

    /**
     * Delete inventory report
     */
    public function deleteReport($id)
    {
        try {
            $report = InventoryCheck::findOrFail($id);

            // Log the deletion for audit purposes
            \Log::info('🗑️ Cook deleting inventory report', [
                'report_id' => $id,
                'submitted_by' => $report->user->name ?? 'Unknown',
                'submitted_at' => $report->created_at,
                'deleted_by' => Auth::user()->name,
                'deleted_at' => now()
            ]);

            // Delete related items first (cascade should handle this, but being explicit)
            $report->items()->delete();

            // Delete the report
            $report->delete();

            // Send notification to kitchen staff about deletion
            try {
                $notificationService = new NotificationService();
                $notificationService->inventoryReportDeleted([
                    'report_id' => $id,
                    'submitted_by' => $report->user_id,
                    'deleted_by' => Auth::user()->name,
                    'reason' => 'Report deleted by cook/admin'
                ]);
            } catch (\Exception $e) {
                // Continue even if notification fails
                \Log::warning('Failed to send deletion notification', ['error' => $e->getMessage()]);
            }

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Inventory report deleted successfully'
                ]);
            }

            return redirect()->back()->with('success', 'Inventory report deleted successfully');

        } catch (\Exception $e) {
            \Log::error('❌ Failed to delete inventory report', [
                'report_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete inventory report'
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to delete inventory report');
        }
    }

    /**
     * Clear all inventory reports
     */
    public function clearAllReports()
    {
        try {
            // Get all reports for logging
            $reports = InventoryCheck::with('user')->get();
            $totalReports = $reports->count();

            if ($totalReports === 0) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No inventory reports to delete'
                    ]);
                }
                return redirect()->back()->with('info', 'No inventory reports to delete');
            }

            // Log the bulk deletion for audit purposes
            \Log::info('🗑️ Cook clearing all inventory reports', [
                'total_reports' => $totalReports,
                'deleted_by' => Auth::user()->name,
                'deleted_at' => now(),
                'reports' => $reports->map(function($report) {
                    return [
                        'id' => $report->id,
                        'submitted_by' => $report->user->name ?? 'Unknown',
                        'submitted_at' => $report->created_at
                    ];
                })
            ]);

            // Get unique submitters for notifications
            $submitterIds = $reports->pluck('user_id')->unique()->filter();

            // Delete all items first
            InventoryCheckItem::whereIn('inventory_check_id', $reports->pluck('id'))->delete();

            // Delete all reports
            InventoryCheck::whereIn('id', $reports->pluck('id'))->delete();

            // Send notifications to all kitchen staff who had reports deleted
            try {
                $notificationService = new NotificationService();
                foreach ($submitterIds as $submitterId) {
                    $userReports = $reports->where('user_id', $submitterId);
                    $notificationService->inventoryReportsCleared([
                        'total_reports' => $userReports->count(),
                        'submitted_by' => $submitterId,
                        'deleted_by' => Auth::user()->name,
                        'reason' => 'All inventory reports cleared by cook/admin'
                    ]);
                }
            } catch (\Exception $e) {
                // Continue even if notification fails
                \Log::warning('Failed to send bulk deletion notifications', ['error' => $e->getMessage()]);
            }

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Successfully deleted {$totalReports} inventory reports"
                ]);
            }

            return redirect()->back()->with('success', "Successfully deleted {$totalReports} inventory reports");

        } catch (\Exception $e) {
            \Log::error('❌ Failed to clear all inventory reports', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to clear inventory reports'
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to clear inventory reports');
        }
    }
}
