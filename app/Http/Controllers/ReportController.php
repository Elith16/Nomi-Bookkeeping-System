<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Inventory;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function dailyReport(Request $request)
    {
        // Default to current day if no date selected
        $date = $request->input('date') ?? Carbon::today()->toDateString();

        // Filter by selected item and/or staff if provided
        $query = Transaction::whereDate('created_at', $date);

        if ($request->has('inventory_item')) {
            $query->whereHas('transactionItems', function($q) use ($request) {
                $q->where('inventory_id', $request->inventory_item);
            });
        }

        if ($request->has('staff_id')) {
            $query->where('user_id', $request->staff_id);
        }

        // Get the transactions and calculate total sales
        $transactions = $query->with('transactionItems')->get();
        $totalSales = $transactions->sum('total_price');

        return view('reports.daily', compact('transactions', 'totalSales', 'date'));
    }

    public function monthlyReport(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $inventoryItems = Inventory::all();
        $staff = User::all();
        
        // Query for transactions filtered by month, inventory item, and staff
        $query = Transaction::whereMonth('created_at', Carbon::parse($month)->month)
                            ->whereYear('created_at', Carbon::parse($month)->year);

        if ($request->has('inventory_item') && $request->inventory_item != '') {
            $query->whereHas('transactionItems', function ($q) use ($request) {
                $q->where('inventory_id', $request->inventory_item);
            });
        }

        if ($request->has('staff_id') && $request->staff_id != '') {
            $query->where('user_id', $request->staff_id);
        }

        $transactions = $query->with('transactionItems', 'customer')->get();
        return view('reports.monthly', compact('transactions', 'inventoryItems', 'staff', 'month'));
    }
}
