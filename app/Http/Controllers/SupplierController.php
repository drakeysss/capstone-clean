<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::all();
        return view('cook.suppliers', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:255',
        ]);

        Supplier::create($validated);

        return redirect()->route('cook.suppliers.index')
            ->with('success', 'Supplier added successfully.');
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:255',
        ]);

        $supplier->update($validated);

        return redirect()->route('cook.suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('cook.suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }

    public function purchaseOrder(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'items' => 'required|array',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit' => 'required|string',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $purchaseOrder = PurchaseOrder::create([
            'supplier_id' => $validated['supplier_id'],
            'status' => 'pending',
            'total_amount' => collect($validated['items'])->sum(function ($item) {
                return $item['quantity'] * $item['unit_price'];
            }),
        ]);

        foreach ($validated['items'] as $item) {
            $purchaseOrder->items()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['quantity'] * $item['unit_price'],
            ]);
        }

        return redirect()->route('cook.suppliers')
            ->with('success', 'Purchase order created successfully.');
    }
} 