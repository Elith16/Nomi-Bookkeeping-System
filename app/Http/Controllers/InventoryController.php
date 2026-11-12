<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Order the inventory items by category
        $inventories = Inventory::orderBy('category')->get();
        
        return view('inventories.index', compact('inventories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::all(); // Fetch all suppliers
        return view('inventories.create', compact('suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric',
            'supplier_id' => 'required|exists:suppliers,id' // Validate supplier exists
        ]);

        Inventory::create($validated);
        return redirect()->route('inventories.index')->with('success', 'Inventory item created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Inventory $inventory)
    {
        return view('inventories.show', compact('inventory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inventory $inventory)
    {
        $suppliers = Supplier::all(); // Fetch all suppliers
        return view('inventories.edit', compact('inventory', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric',
            'supplier_id' => 'required|exists:suppliers,id' // Validate supplier exists
        ]);

        $inventory->update($validated);
        return redirect()->route('inventories.index')->with('success', 'Inventory item updated successfully.');
    }

    /**
     * Display the Instock page for a supplier.
     */
    public function instock($id)
    {
        // Fetch the supplier
        $supplier = Supplier::findOrFail($id);
        
        // Fetch inventory items for this supplier
        $inventories = Inventory::where('supplier_id', $supplier->id)->get();
        
        // Fetch all suppliers for dropdown options
        $suppliers = Supplier::all();
        
        return view('inventories.instock', compact('supplier', 'inventories', 'suppliers'));
    }

    public function outstock($id)
    {
        // Fetch the supplier
        $supplier = Supplier::findOrFail($id);
        
        // Fetch inventory items for this supplier
        $inventories = Inventory::where('supplier_id', $supplier->id)->get();
        
        // Fetch all suppliers for dropdown options
        $suppliers = Supplier::all();
        
        return view('inventories.outstock', compact('supplier', 'inventories', 'suppliers'));
    }

    public function updateStock(Request $request, $id)
    {
        // Use findOrFail to get the specific inventory item
        $inventory = Inventory::findOrFail($request->input('inventory_id'));

        if ($inventory) {
            // Add quantity from form to the existing stock
            $quantity = $request->input('quantity');
            $inventory->quantity += $quantity;
            $inventory->save();

            return redirect()->route('inventories.index')->with('success', 'Stock updated successfully.');
        } else {
            return redirect()->route('inventories.index')->with('error', 'Inventory item not found.');
        }
    }

    public function getInventoryBySupplier($supplierId)
    {
        // Fetch the inventories based on the supplier ID
        $inventories = Inventory::where('supplier_id', $supplierId)->get();
        Log::info('Fetching inventories for supplier ID: ' . $supplierId);
        Log::info($inventories);

        return response()->json($inventories);
    }
}
