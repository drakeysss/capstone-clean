<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PurchaseOrderController extends Controller
{
    /**
     * Display purchase orders for kitchen staff
     */
    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['creator', 'approver', 'deliveryConfirmer', 'items.inventoryItem']);

        // Kitchen staff mainly sees approved and delivered orders
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        } else {
            // Default to showing approved and delivered orders
            $query->whereIn('status', ['approved', 'ordered', 'delivered']);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->where('order_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('order_date', '<=', $request->date_to);
        }

        $purchaseOrders = $query->orderBy('created_at', 'desc')->paginate(15)->appends($request->query());

        return view('kitchen.purchase-orders.index', compact('purchaseOrders'));
    }

    /**
     * Show purchase order details for kitchen staff
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['creator', 'approver', 'deliveryConfirmer', 'items.inventoryItem']);
        
        return view('kitchen.purchase-orders.show', compact('purchaseOrder'));
    }

    /**
     * Show delivery confirmation form
     */
    public function confirmDelivery(PurchaseOrder $purchaseOrder)
    {
        if (!$purchaseOrder->canBeDelivered()) {
            return redirect()->back()->with('error', 'This purchase order cannot be marked as delivered.');
        }

        $purchaseOrder->load(['items.inventoryItem']);
        
        return view('kitchen.purchase-orders.confirm-delivery', compact('purchaseOrder'));
    }

    /**
     * Process delivery confirmation
     */
    public function processDelivery(Request $request, PurchaseOrder $purchaseOrder)
    {
        if (!$purchaseOrder->canBeDelivered()) {
            return redirect()->back()->with('error', 'This purchase order cannot be marked as delivered.');
        }

        $validator = Validator::make($request->all(), [
            'actual_delivery_date' => 'required|date',
            'delivery_notes' => 'nullable|string|max:1000',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:purchase_order_items,id',
            'items.*.quantity_delivered' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Update purchase order items with delivered quantities
            foreach ($request->items as $itemData) {
                $item = PurchaseOrderItem::find($itemData['id']);
                $item->update([
                    'quantity_delivered' => $itemData['quantity_delivered'],
                    'notes' => $itemData['notes'] ?? $item->notes
                ]);
            }

            // Mark purchase order as delivered
            $purchaseOrder->markAsDelivered(
                Auth::user()->user_id,
                $request->actual_delivery_date
            );

            // Update notes if provided
            if ($request->delivery_notes) {
                $purchaseOrder->update([
                    'notes' => $purchaseOrder->notes . "\n\nDelivery Notes: " . $request->delivery_notes
                ]);
            }

            // Send notification to cook
            $notificationService = new NotificationService();
            $notificationService->purchaseOrderDelivered($purchaseOrder);

            DB::commit();

            return redirect()->route('kitchen.purchase-orders.show', $purchaseOrder)
                ->with('success', 'Delivery confirmed successfully! Inventory has been updated.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to confirm delivery: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Get pending deliveries for dashboard
     */
    public function getPendingDeliveries()
    {
        $pendingDeliveries = PurchaseOrder::whereIn('status', ['approved', 'ordered'])
            ->with(['creator', 'items.inventoryItem'])
            ->orderBy('expected_delivery_date')
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'created_by' => $order->creator->user_fname . ' ' . $order->creator->user_lname,
                    'order_date' => $order->order_date->format('M d, Y'),
                    'expected_delivery' => $order->expected_delivery_date ? 
                                         $order->expected_delivery_date->format('M d, Y') : 'Not set',
                    'total_amount' => $order->total_amount,
                    'items_count' => $order->items->count(),
                    'status' => $order->status,
                    'is_overdue' => $order->expected_delivery_date && 
                                   $order->expected_delivery_date->isPast()
                ];
            });

        return response()->json([
            'success' => true,
            'deliveries' => $pendingDeliveries
        ]);
    }

    /**
     * Quick delivery confirmation (for simple cases)
     */
    public function quickConfirmDelivery(PurchaseOrder $purchaseOrder)
    {
        if (!$purchaseOrder->canBeDelivered()) {
            return response()->json([
                'success' => false,
                'message' => 'This purchase order cannot be marked as delivered.'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Mark all items as fully delivered
            foreach ($purchaseOrder->items as $item) {
                $item->update(['quantity_delivered' => $item->quantity_ordered]);
            }

            // Mark purchase order as delivered
            $purchaseOrder->markAsDelivered(Auth::user()->user_id);

            // Send notification to cook
            $notificationService = new NotificationService();
            $notificationService->purchaseOrderDelivered($purchaseOrder);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Delivery confirmed successfully! Inventory has been updated.'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm delivery: ' . $e->getMessage()
            ], 500);
        }
    }
}
