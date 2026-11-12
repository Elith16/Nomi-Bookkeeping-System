<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch only active customers
        $customers = Customer::where('active', true)->get();

        return view('customers.index', compact('customers'));
    }

     /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:10',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:customers',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:200',
            'prescription' => 'nullable|string|max:100',
            'remarks' => 'nullable|string|max:250',
        ]);

        Customer::create($validated);
        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        // Adjusting the relationship loading to reflect the correct structure
        $customer->load('transactions.transactionItems.inventory', 'transactions.user');
        return view('customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:10',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:customers,email,' . $customer->id,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:200',
            'prescription' => 'nullable|string|max:100',
            'remarks' => 'nullable|string|max:250',
            'status' => 'active', // Set customer as active by default
        ]);

        $customer->update($validated);
        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        // Instead of deleting, deactivate the customer
        $customer->update(['active' => false]);

        return redirect()->route('customers.index')->with('success', 'Customer deactivated successfully.');
    }

    public function storeAjax(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // Add any other validation rules for new customers here
        ]);

        $customer = Customer::create($validated);

        return response()->json(['success' => true, 'customer_id' => $customer->id]);
    }
}
