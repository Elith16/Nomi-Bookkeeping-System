<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::all();
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        // Validation for supplier creation
        $validatedData = $request->validate([
            'name' => 'required|string|regex:/^[a-zA-Z\s]+$/|max:255',
            'contact' => 'required|numeric|digits_between:9,20',
            'address' => 'required|string|max:100',
        ]);

        // Ensure the user is authenticated before proceeding
        $userId = Auth::id(); // Use the authenticated user's ID

        // Create supplier with the current authenticated user as the creator
        Supplier::create([
            'name' => $validatedData['name'],
            'contact' => $validatedData['contact'],
            'address' => $validatedData['address'],
            'user_id' => $userId, // Correctly access the user's id
        ]);

        return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully.');
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        // Validate request data
        $validatedData = $request->validate([
            'name' => 'required|string|regex:/^[a-zA-Z\s]+$/|max:255',
            'contact' => 'required|numeric|digits_between:10,20',
            'address' => 'required|string|max:100',
        ]);

        // Update the supplier record
        $supplier->update($validatedData);

        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    public function show(Supplier $supplier)
    {
        return view('suppliers.show', compact('supplier'));
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully.');
    }
}
