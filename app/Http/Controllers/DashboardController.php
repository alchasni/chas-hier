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
        $date = date('Y-m-d');
        $transaction = Transaction::where('created_at', 'LIKE', "%$date%")->count();
        $category = Category::count();
        $product = Product::count();
        $guest = Guest::count();

        if (auth()->user()->level == 1) {
            return view('admin.dashboard', compact('category', 'product', 'guest', 'transaction'));
        } else {
            return view('kasir.dashboard');
        }
    }
}
