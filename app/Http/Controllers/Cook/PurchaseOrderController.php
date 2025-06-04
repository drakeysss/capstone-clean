<?php

namespace App\Http\Controllers\Cook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Ingredient;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the purchase orders.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with(['supplier', 'items'])->orderBy('created_at', 'desc')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $ingredients = Ingredient::orderBy('name')->get();
        
        return view('cook.purchase-orders', compact('purchaseOrders', 'suppliers', 'ingredients'));
    }

    /**
     * Store a newly created purchase order in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,id',
            'expected_delivery_date' => 'required|date|after:today',
            'items' => 'required|array|min:1',
            'items.*.ingredient_id' => 'required|exists:ingredients,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Create purchase order
        $purchaseOrder = PurchaseOrder::create([
            'supplier_id' => $request->supplier_id,
            'user_id' => Auth::id(),
            'order_date' => now(),
            'expected_delivery_date' => $request->expected_delivery_date,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        // Add items to purchase order
        foreach ($request->items as $item) {
            $purchaseOrder->items()->create([
                'ingredient_id' => $item['ingredient_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['quantity'] * $item['unit_price'],
            ]);
        }

        return redirect()->route('cook.purchase-orders')
            ->with('success', 'Purchase order created successfully.');
    }

    /**
     * Update the specified purchase order in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,approved,received,cancelled',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $purchaseOrder->update([
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        // If the order is received, update inventory
        if ($request->status === 'received') {
            foreach ($purchaseOrder->items as $item) {
                $ingredient = Ingredient::find($item->ingredient_id);
                if ($ingredient) {
                    // Assuming there's a stock field in the ingredients table
                    $ingredient->stock += $item->quantity;
                    $ingredient->save();
                }
            }
        }

        return redirect()->route('cook.purchase-orders')
            ->with('success', 'Purchase order updated successfully.');
    }

    /**
     * Remove the specified purchase order from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        
        // Only allow deletion of pending orders
        if ($purchaseOrder->status !== 'pending') {
            return redirect()->route('cook.purchase-orders')
                ->with('error', 'Only pending purchase orders can be deleted.');
        }
        
        // Delete related items first
        $purchaseOrder->items()->delete();
        $purchaseOrder->delete();

        return redirect()->route('cook.purchase-orders')
            ->with('success', 'Purchase order deleted successfully.');
    }
}
