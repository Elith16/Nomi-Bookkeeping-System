<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Inventory;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');

        // Search logic for different modules
        $customer = Customer::where('name', 'LIKE', "%$query%")->first();
        $inventory = Inventory::where('name', 'LIKE', "%$query%")->first();
        
        // Redirect logic
        if ($customer) {
            return redirect()->route('customers.show', $customer->id);
        } elseif ($inventory) {
            return redirect()->route('inventories.edit', $inventory->id);
        } else {
            // If no results are found, show a generic results page or a "No results found" message
            return view('search.no_results', compact('query'));
        }
    }

    // Method to handle AJAX suggestions
    public function suggestions(Request $request)
    {
        $search = $request->input('search');

        // Search for customers and inventory items that match the input
        $customers = Customer::where('name', 'LIKE', "%{$search}%")->get();
        $inventories = Inventory::where('name', 'LIKE', "%{$search}%")->get();

        // Merge both collections and convert to an array of names
        $suggestions = $customers->concat($inventories)->map(function($item) {
            return [
                'id' => $item->id,
                'name' => $item->name
            ];
        });

        return response()->json($suggestions);
    }
}
