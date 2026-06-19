<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $totalRevenue  = Order::where('status', 'shipped')->sum('total_price');
        $lowStockCount = Product::where('stock', '<', 10)->count();
        $recentOrders  = Order::with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalProducts',
            'pendingOrders',
            'totalRevenue',
            'lowStockCount',
            'recentOrders'
        ));
    }
}
