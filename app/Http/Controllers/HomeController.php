<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // explicitly select the category column before distinct to satisfy static analysis
        $categories = Product::select('category')->distinct()->orderBy('category')->pluck('category');
        $products   = Product::query()->orderBy('name')->get();
        $cartCount  = Auth::check() ? array_sum(session('cart', [])) : 0;

        return view('home.index', compact('products', 'categories', 'cartCount'));
    }
}
