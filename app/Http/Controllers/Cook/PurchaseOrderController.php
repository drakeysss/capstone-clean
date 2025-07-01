<?php

namespace App\Http\Controllers\Cook;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Inventory;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PurchaseOrderController extends Controller
{
    /**
     * Display purchase orders list
     */
    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['creator', 'approver', 'deliveryConfirmer', 'items.inventoryItem']);

        // Apply filters
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->where('order_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('order_date', '<=', $request->date_to);
        }

        $purchaseOrders = $query->orderBy('created_at', 'desc')->paginate(15)->appends($request->query());

        // Get statistics
        $stats = [
            'pending_orders' => PurchaseOrder::pending()->count(),
            'approved_orders' => PurchaseOrder::approved()->count(),
            'delivered_orders' => PurchaseOrder::delivered()->count()
        ];

        return view('cook.purchase-orders.index', compact('purchaseOrders', 'stats'));
    }

    /**
     * Show create purchase order form
     */
    public function create()
    {
        // Get low stock items that need reordering
        $lowStockItems = Inventory::lowStock()->get();
        $allItems = Inventory::orderBy('name')->get();

        return view('cook.purchase-orders.create', compact('lowStockItems', 'allItems'));
    }

    /**
     * Store new purchase order
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after:order_date',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.inventory_id' => 'required|exists:inventory,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Create purchase order
            $purchaseOrder = PurchaseOrder::create([
                'created_by' => Auth::user()->user_id,
                'status' => 'pending',
                'order_date' => $request->order_date,
                'expected_delivery_date' => $request->expected_delivery_date,
                'notes' => $request->notes
            ]);

            // Create purchase order items
            $totalAmount = 0;
            foreach ($request->items as $itemData) {
                $inventoryItem = Inventory::find($itemData['inventory_id']);
                
                $item = PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'inventory_id' => $itemData['inventory_id'],
                    'item_name' => $inventoryItem->name,
                    'quantity_ordered' => $itemData['quantity'],
                    'unit' => $inventoryItem->unit,
                    'unit_price' => $itemData['unit_price'],
                    'notes' => $itemData['notes'] ?? null
                ]);

                $totalAmount += $item->total_price;
            }

            // Update total amount
            $purchaseOrder->update(['total_amount' => $totalAmount]);

            // Send notification to kitchen staff
            $notificationService = new NotificationService();
            $notificationService->purchaseOrderCreated($purchaseOrder);

            DB::commit();

            return redirect()->route('cook.purchase-orders.show', $purchaseOrder)
                ->with('success', 'Purchase order created successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to create purchase order: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show purchase order details
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['creator', 'approver', 'deliveryConfirmer', 'items.inventoryItem']);
        
        return view('cook.purchase-orders.show', compact('purchaseOrder'));
    }

    /**
     * Approve purchase order
     */
    public function approve(PurchaseOrder $purchaseOrder)
    {
        if (!$purchaseOrder->canBeApproved()) {
            return redirect()->back()->with('error', 'Purchase order cannot be approved.');
        }

        $purchaseOrder->approve(Auth::user()->user_id);

        // Send notification to kitchen staff
        $notificationService = new NotificationService();
        $notificationService->purchaseOrderApproved($purchaseOrder);

        return redirect()->back()->with('success', 'Purchase order approved successfully!');
    }

    /**
     * Cancel purchase order
     */
    public function cancel(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status === 'delivered') {
            return redirect()->back()->with('error', 'Cannot cancel delivered purchase order.');
        }

        $purchaseOrder->update(['status' => 'cancelled']);

        return redirect()->back()->with('success', 'Purchase order cancelled successfully!');
    }

    /**
     * Get low stock items for AJAX
     */
    public function getLowStockItems()
    {
        $lowStockItems = Inventory::lowStock()
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'current_quantity' => $item->quantity,
                    'reorder_point' => $item->reorder_point,
                    'unit' => $item->unit,
                    'unit_price' => $item->unit_price,
                    'shortage' => max(0, $item->reorder_point - $item->quantity)
                ];
            });

        return response()->json([
            'success' => true,
            'items' => $lowStockItems
        ]);
    }

    /**
     * Generate purchase order suggestions based on low stock
     */
    public function generateSuggestions()
    {
        $suggestions = Inventory::lowStock()
            ->get()
            ->map(function ($item) {
                $suggestedQuantity = max(
                    $item->reorder_point * 2, // Order double the reorder point
                    $item->reorder_point - $item->quantity + 10 // Or shortage plus buffer
                );

                return [
                    'inventory_id' => $item->id,
                    'name' => $item->name,
                    'current_stock' => $item->quantity,
                    'reorder_point' => $item->reorder_point,
                    'suggested_quantity' => $suggestedQuantity,
                    'unit' => $item->unit,
                    'unit_price' => $item->unit_price,
                    'estimated_cost' => $suggestedQuantity * $item->unit_price
                ];
            });

        return response()->json([
            'success' => true,
            'suggestions' => $suggestions
        ]);
    }
}
