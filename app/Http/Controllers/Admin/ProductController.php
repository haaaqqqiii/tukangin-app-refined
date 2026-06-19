<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('name')->get();
        return view('admin.products.index', compact('products'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        Product::create($data);

        return back()->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validated($request);
        $product->update($data);

        return back()->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return back()->with('success', 'Produk berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'        => 'required|string|max:255',
            'category'    => 'required|string|max:100',
            'price'       => 'required|integer|min:0',
            'unit'        => 'required|string|max:50',
            'image'       => 'required|url|max:2048',
            'description' => 'required|string|max:1000',
            'stock'       => 'required|integer|min:0',
        ]);
    }
}
