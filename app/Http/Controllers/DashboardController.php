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
        $data = $this->getDashboardData();
        return $this->getDashboardView($data);
    }

    protected function getDashboardData()
    {
        $date = now()->format('Y-m-d');
        return [
            'transaction' => Transaction::where('created_at', 'LIKE', "%$date%")->count(),
            'category' => Category::count(),
            'product' => Product::count(),
            'guest' => Guest::count(),
        ];
    }

    protected function getDashboardView($data)
    {
        if (auth()->user()->level == 1) {
            return view('dashboard.1', $data);
        } elseif (auth()->user()->level == 2) {
            return view('dashboard.3');
        } else {
            abort(403, 'Unauthorized action.');
        }
    }
}
