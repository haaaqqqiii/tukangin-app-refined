<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        [$cartItems, $total] = $this->buildCartData();

        return view('cart.index', compact('cartItems', 'total'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $product  = Product::findOrFail($request->product_id);
        $quantity = (int) $request->quantity;
        $cart     = session('cart', []);
        $current  = $cart[$product->id] ?? 0;
        $newQty   = $current + $quantity;

        if ($newQty > $product->stock) {
            return back()->with('error', "Stok tidak mencukupi. Stok tersedia: {$product->stock} {$product->unit}.");
        }

        $cart[$product->id] = $newQty;
        session(['cart' => $cart]);

        return back()->with('success', "{$product->name} ditambahkan ke keranjang.");
    }

    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:0',
        ]);

        $product = Product::findOrFail($request->product_id);
        $cart    = session('cart', []);

        if ((int) $request->quantity <= 0) {
            unset($cart[$product->id]);
            session(['cart' => $cart]);
            return back()->with('success', 'Item dihapus dari keranjang.');
        }

        if ((int) $request->quantity > $product->stock) {
            return back()->with('error', "Stok tidak mencukupi. Maksimal: {$product->stock} {$product->unit}.");
        }

        $cart[$product->id] = (int) $request->quantity;
        session(['cart' => $cart]);

        return back()->with('success', 'Keranjang diperbarui.');
    }

    public function remove(Request $request)
    {
        $request->validate(['product_id' => 'required|integer']);

        $cart = session('cart', []);
        unset($cart[$request->product_id]);
        session(['cart' => $cart]);

        return back()->with('success', 'Item dihapus dari keranjang.');
    }

    public function checkout(Request $request)
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return back()->with('error', 'Keranjang Anda kosong.');
        }

        $products = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');
        $total    = 0;

        foreach ($cart as $productId => $qty) {
            if (!$products->has($productId)) {
                return back()->with('error', 'Produk tidak ditemukan.');
            }
            $product = $products[$productId];
            if ($qty > $product->stock) {
                return back()->with('error', "Stok {$product->name} tidak mencukupi (tersisa {$product->stock}).");
            }
            $total += $product->price * $qty;
        }

        $order = Order::create([
            'user_id'     => Auth::id(),
            'total_price' => $total,
            'status'      => 'pending',
        ]);

        foreach ($cart as $productId => $qty) {
            $product = $products[$productId];
            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $productId,
                'quantity'   => $qty,
                'price'      => $product->price,
            ]);
            $product->decrement('stock', $qty);
        }

        session()->forget('cart');

        $formatted = 'Rp ' . number_format($total, 0, ',', '.');

        return redirect()->route('orders.index')
            ->with('success', "Checkout berhasil! Total pembayaran: {$formatted}.");
    }

    private function buildCartData(): array
    {
        $cart      = session('cart', []);
        $cartItems = [];
        $total     = 0;

        if (!empty($cart)) {
            $products = Product::whereIn('id', array_keys($cart))->get()->keyBy('id');
            foreach ($cart as $productId => $qty) {
                if ($products->has($productId)) {
                    $product     = $products[$productId];
                    $subtotal    = $product->price * $qty;
                    $total      += $subtotal;
                    $cartItems[] = ['product' => $product, 'quantity' => $qty, 'subtotal' => $subtotal];
                }
            }
        }

        return [$cartItems, $total];
    }
}
