<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Inventory;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function create()
    {
        // Retrieve the necessary data for the form (like customers and inventory items)
        $customers = Customer::where('active', true)->get();
        $inventories = Inventory::all();

        // Return the view with the data
        return view('transactions.create', compact('customers', 'inventories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer' => 'required|exists:customers,name',
            'customer_id' => 'required|exists:customers,id',
            'inventory_items' => 'required|array',
            'inventory_items.*.inventory' => 'required|exists:inventories,name',
            'inventory_items.*.quantity' => 'required|integer|min:1',
            'total_price' => 'required|numeric|min:0',
        ]);

        // Check for duplicate inventory items
        $inventoryNames = array_column($validated['inventory_items'], 'inventory');
        if (count($inventoryNames) !== count(array_unique($inventoryNames))) {
            return back()->withErrors(['message' => 'You cannot select the same inventory item multiple times.']);
        }

        // Find the customer based on the name
        $customer = Customer::where('name', $validated['customer'])->first();

        if (!$customer) {
            return back()->withErrors(['message' => 'Customer not found.']);
        }

        // Create the transaction record
        $transaction = Transaction::create([
            'customer_id' => $customer->id,
            'user_id' => Auth::id(),
            'total_price' => $validated['total_price'],
            'active' => true,
        ]);

        $lowStockItems = [];

        foreach ($validated['inventory_items'] as $item) {
            $inventory = Inventory::where('name', $item['inventory'])->first();

            if ($inventory->quantity < $item['quantity']) {
                return back()->withErrors(['message' => 'The entered item is not available at the moment.']);
            }

            $inventory->decrement('quantity', $item['quantity']);

            if ($inventory->quantity < 100) {
                $lowStockItems[] = $inventory->name;
            }

            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'inventory_id' => $inventory->id,
                'quantity' => $item['quantity'],
                'price' => $inventory->price,
            ]);
        }

        if (!empty($lowStockItems)) {
            session()->flash('low_stock_alert', 'The following items are low in stock: ' . implode(', ', $lowStockItems));
        }

        return redirect()->route('transactions.index')->with('success', 'Transaction recorded successfully.');
    }

    public function show($id)
    {
       // Eager load the transactionItems and the nested inventory relationship
        $transaction = Transaction::with(['transactionItems.inventory', 'customer', 'user'])->findOrFail($id);
        
        return view('transactions.show', compact('transaction'));
    }

    public function edit($id)
    {
        $transaction = Transaction::with('transactionItems.inventory', 'customer')->findOrFail($id);
        $customers = Customer::where('active', true)->get();
        $inventories = Inventory::all();

        return view('transactions.edit', compact('transaction', 'customers', 'inventories'));
    }

    public function update(Request $request, $id)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'customer' => 'required|exists:customers,name',
                'inventory_items' => 'required|array',
                'inventory_items.*.inventory' => 'required|exists:inventories,name',
                'inventory_items.*.quantity' => 'required|integer|min:1',
                'total_price' => 'required|numeric|min:0',
            ]);

            // Find the customer based on the name
            $customer = Customer::where('name', $validated['customer'])->first();

            if (!$customer) {
                return back()->withErrors(['message' => 'Customer not found.']);
            }

            // Check for duplicate inventory items
            $inventoryNames = array_column($validated['inventory_items'], 'inventory');
            if (count($inventoryNames) !== count(array_unique($inventoryNames))) {
                return back()->withErrors(['message' => 'Duplicate inventory items selected in the transaction.']);
            }

            // Find the transaction record
            $transaction = Transaction::findOrFail($id);

            // Loop through the existing transaction items to restore the inventory
            foreach ($transaction->transactionItems as $transactionItem) {
                $transactionItem->inventory->increment('quantity', $transactionItem->quantity);
                $transactionItem->delete();
            }

            // Update the transaction record
            $transaction->update([
                'customer_id' => $customer->id,
                'user_id' => Auth::id(),
                'total_price' => 0, // Reset the total price to calculate it from scratch
            ]);

            $totalPrice = 0;

            foreach ($validated['inventory_items'] as $item) {
                $inventory = Inventory::where('name', $item['inventory'])->first();

                if ($inventory->quantity < $item['quantity']) {
                    return back()->withErrors(['message' => 'The entered item is not available at the moment.']);
                }

                // Decrease the inventory quantity
                $inventory->decrement('quantity', $item['quantity']);

                // Calculate the total price for this item
                $itemPrice = $inventory->price * $item['quantity'];
                $totalPrice += $itemPrice;

                // Create the transaction item for each inventory item
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'inventory_id' => $inventory->id,
                    'quantity' => $item['quantity'],
                    'price' => $inventory->price,
                ]);
            }

            // Update the total price of the transaction
            $transaction->update([
                'total_price' => $totalPrice,
            ]);

            return redirect()->route('transactions.index')->with('success', 'Transaction updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    public function index()
    {
        // Fetch only active transactions
        $transactions = Transaction::with(['customer', 'user', 'transactionItems.inventory'])
            ->where('active', true)
            ->get();

        return view('transactions.index', compact('transactions'));
    }

    public function requestVoid(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->update([
            'void_requested' => true,
            'void_description' => $request->input('void_description'),
        ]);

        return back()->with('success', 'Void request sent successfully.');
    }

    public function approveVoid($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->update([
            'voided' => true,
        ]);

        return back()->with('success', 'Transaction voided successfully.');
    }

    public function destroy($id)
    {
        try {
            // Find the transaction
            $transaction = Transaction::findOrFail($id);

            // Mark the transaction as inactive instead of deleting it
            $transaction->update(['active' => false]);

            return redirect()->route('transactions.index')->with('success', 'Transaction marked as inactive.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }
}
