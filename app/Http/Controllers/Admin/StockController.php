<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('stock')->get();
        return view('admin.stock.index', compact('products'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate(['stock' => 'required|integer|min:0']);
        $product->update(['stock' => $request->stock]);

        return back()->with('success', "Stok {$product->name} berhasil diperbarui menjadi {$request->stock} {$product->unit}.");
    }
}
