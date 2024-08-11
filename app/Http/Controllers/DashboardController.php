<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Guest;
use App\Models\Transaction;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-d');
        $category = Category::count();
        $product = Product::count();
        $guest = Guest::count();
        $transaction = Transaction::where('created_at', 'LIKE', "%$end_date%")->count();
        $dates = array();
        $incomes = array();

        while (strtotime($start_date) <= strtotime($end_date)) {
            $dates[] = (int) substr($start_date, 8, 2);
            $income = Transaction::where('created_at', 'LIKE', "%$start_date%")->sum('final_price');
            $incomes[] += $income;
            $start_date = date('Y-m-d', strtotime("+1 day", strtotime($start_date)));
        }

        if (auth()->user()->level == 1) {
            return view('admin.dashboard', compact('category', 'product', 'guest', 'transaction', 'start_date', 'end_date', 'dates', 'incomes'));
        } else {
            return view('kasir.dashboard');
        }
    }
}
