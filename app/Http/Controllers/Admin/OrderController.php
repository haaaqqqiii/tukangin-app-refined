<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'items.product'])->latest()->get();
        return view('admin.orders.index', compact('orders'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,rejected',
        ]);

        $order->update(['status' => $request->status]);

        $messages = [
            'processing' => 'Pesanan sedang diproses.',
            'shipped'    => 'Pesanan telah dikirim.',
            'rejected'   => 'Pesanan telah ditolak.',
            'pending'    => 'Pesanan dikembalikan ke status pending.',
        ];

        return back()->with('success', $messages[$request->status]);
    }
}
