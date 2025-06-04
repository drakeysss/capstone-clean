<?php

namespace App\Http\Controllers\Cook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventory;

class InventoryController extends Controller
{
    public function index()
    {
        $inventory = Inventory::all();
        return view('cook.inventory', compact('inventory'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'quantity' => 'required|numeric',
            'unit' => 'required|string|max:50',
            'category' => 'required|string|max:100',
            'expiry_date' => 'nullable|date',
            'minimum_stock' => 'required|numeric'
        ]);

        Inventory::create($validated);
        return redirect()->route('cook.inventory')->with('success', 'Item added successfully');
    }

    public function update(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'quantity' => 'required|numeric',
            'unit' => 'required|string|max:50',
            'category' => 'required|string|max:100',
            'expiry_date' => 'nullable|date',
            'minimum_stock' => 'required|numeric'
        ]);

        $inventory->update($validated);
        return redirect()->route('cook.inventory')->with('success', 'Item updated successfully');
    }

    public function destroy(Inventory $inventory)
    {
        $inventory->delete();
        return redirect()->route('cook.inventory')->with('success', 'Item deleted successfully');
    }

    public function updateStock(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'quantity' => 'required|numeric',
            'operation' => 'required|in:add,subtract'
        ]);

        $newQuantity = $validated['operation'] === 'add' 
            ? $inventory->quantity + $validated['quantity']
            : $inventory->quantity - $validated['quantity'];

        if ($newQuantity < 0) {
            return back()->withErrors(['quantity' => 'Insufficient stock']);
        }

        $inventory->update(['quantity' => $newQuantity]);
        return redirect()->route('cook.inventory')->with('success', 'Stock updated successfully');
    }

    public function lowStock()
    {
        $lowStock = Inventory::where('quantity', '<=', \DB::raw('minimum_stock'))->get();
        
        // Calculate budget impact of restocking low inventory items
        $restockBudgetImpact = 0;
        foreach ($lowStock as $item) {
            $quantityNeeded = $item->minimum_stock - $item->quantity;
            $estimatedCost = $quantityNeeded * ($item->unit_price ?? 0);
            $restockBudgetImpact += $estimatedCost;
        }
        
        return view('cook.inventory', [
            'lowStock' => $lowStock,
            'restockBudgetImpact' => $restockBudgetImpact,
            'viewMode' => 'low-stock'
        ]);
    }

    public function expiringItems()
    {
        $expiringItems = Inventory::whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays(30))
            ->orderBy('expiry_date')
            ->get();
            
        // Calculate potential waste cost from expiring items
        $potentialWasteCost = 0;
        foreach ($expiringItems as $item) {
            $potentialWasteCost += $item->quantity * ($item->unit_price ?? 0);
        }
        
        // Calculate budget impact if items need to be replaced
        $replacementBudgetImpact = $potentialWasteCost;
        
        return view('cook.inventory', [
            'expiringItems' => $expiringItems,
            'potentialWasteCost' => $potentialWasteCost,
            'replacementBudgetImpact' => $replacementBudgetImpact,
            'viewMode' => 'expiring'
        ]);
    }
    
    /**
     * Notify kitchen team about delivery status of an inventory item
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function notifyDelivery(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:inventory,id',
            'quantity' => 'required|numeric|min:0',
            'status' => 'required|in:scheduled,in_transit,delivered',
            'notes' => 'nullable|string'
        ]);
        
        $item = Inventory::findOrFail($validated['item_id']);
        
        // Create a notification record in the database
        // This would typically be stored in a delivery_notifications table
        // For now, we'll just return a success message
        
        // If you have a Notification model, you could use it like this:
        // Notification::create([
        //     'type' => 'delivery',
        //     'item_id' => $validated['item_id'],
        //     'item_name' => $item->name,
        //     'quantity' => $validated['quantity'],
        //     'status' => $validated['status'],
        //     'notes' => $validated['notes'],
        //     'notified_by' => auth()->id(),
        //     'notified_at' => now()
        // ]);
        
        // If the status is 'delivered', you could automatically update the inventory
        // if the kitchen team has approved this functionality
        if ($validated['status'] === 'delivered') {
            // This could be a setting or preference that determines if automatic updates are allowed
            $autoUpdateInventory = false;
            
            if ($autoUpdateInventory) {
                $item->update([
                    'quantity' => $item->quantity + $validated['quantity']
                ]);
            }
        }
        
        return redirect()->route('cook.inventory')
            ->with('success', 'Delivery notification sent to kitchen team successfully');
    }
}
