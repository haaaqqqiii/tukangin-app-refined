<?php

namespace App\Http\Controllers;

use App\Models\Order;
use illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('items.product')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        $cartCount = array_sum(session('cart', []));

        return view('orders.index', compact('orders', 'cartCount'));
    }
}
