<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ingredient;
use App\Models\InventoryCheck;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

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
        // The kitchen staff will manually input inventory items they've counted
        // No need to fetch inventory items from the database
        
        return view('kitchen.inventory.index');
    }
    
    /**
     * Submit an inventory check from kitchen team to cook/admin.
     * This allows kitchen staff to report actual inventory levels.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function submitCheck(Request $request)
    {
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
                    $notification = new \App\Models\Notification([
                        'user_id' => $user->id,
                        'title' => 'New Inventory Check',
                        'message' => 'Kitchen staff has submitted a new inventory count.',
                        'type' => 'inventory',
                        'is_read' => false
                    ]);
                    $notification->save();
                }
            }
        } catch (\Exception $e) {
            // Continue even if notification fails
        }
        
        // Process manually entered inventory items
        if ($request->has('manual_items')) {
            foreach ($request->manual_items as $item) {
                // First, find or create the inventory item
                $inventoryItem = \App\Models\Inventory::firstOrCreate(
                    ['name' => $item['name']],
                    [
                        'quantity' => $item['quantity'],
                        'unit' => $item['unit'],
                        'reorder_point' => 10 // Default reorder point
                    ]
                );
                
                // Create check item for each manually entered item
                $checkItem = new \App\Models\InventoryCheckItem();
                $checkItem->inventory_check_id = $check->id;
                $checkItem->ingredient_id = $inventoryItem->id;
                $checkItem->current_stock = $item['quantity'];
                $checkItem->needs_restock = isset($item['needs_restock']) ? true : false;
                $checkItem->notes = $item['notes'] ?? null;
                $checkItem->save();
                
                // Update the inventory item quantity
                $inventoryItem->quantity = $item['quantity'];
                $inventoryItem->save();
            }
        }

        return redirect()->route('kitchen.inventory')
            ->with('success', 'Inventory check submitted successfully.');
    }
}
