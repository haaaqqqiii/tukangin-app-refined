@extends('layouts.app')
@section('title', 'Keranjang Belanja — Tukangin')
@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Keranjang Belanja</h1>

    @if(empty($cartItems))
        <div class="bg-white rounded-xl shadow-sm p-16 text-center">
            <svg class="w-20 h-20 mx-auto text-gray-200 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <p class="text-gray-500 text-lg mb-4">Keranjang Anda kosong</p>
            <a href="{{ route('home') }}"
               class="inline-block bg-orange-500 hover:bg-orange-600 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition">
                Mulai Belanja
            </a>
        </div>
    @else
        <div class="grid lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-4">
                @foreach($cartItems as $item)
                    <div class="bg-white rounded-xl shadow-sm p-5 flex gap-4 items-start">
                        <img src="{{ $item['product']->image }}" alt="{{ $item['product']->name }}"
                             class="w-20 h-20 object-cover rounded-lg shrink-0">
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-800 mb-0.5 truncate">{{ $item['product']->name }}</h3>
                            <span class="inline-block bg-orange-100 text-orange-600 text-xs px-2 py-0.5 rounded mb-2">
                                {{ $item['product']->category }}
                            </span>
                            <p class="text-orange-600 font-bold">
                                Rp {{ number_format($item['product']->price, 0, ',', '.') }}
                                <span class="text-gray-400 font-normal text-sm">/{{ $item['product']->unit }}</span>
                            </p>
                        </div>
                        <div class="flex flex-col items-end gap-2 shrink-0">
                            <form action="{{ route('cart.update') }}" method="POST" class="flex items-center gap-1">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $item['product']->id }}">
                                <input type="number" name="quantity" value="{{ $item['quantity'] }}"
                                       min="1" max="{{ $item['product']->stock }}"
                                       class="w-16 border border-gray-300 rounded px-2 py-1 text-sm text-center focus:outline-none focus:ring-2 focus:ring-orange-300">
                                <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-2 py-1 rounded text-xs transition">OK</button>
                            </form>
                            <p class="text-sm font-semibold text-gray-700">
                                Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                            </p>
                            <form action="{{ route('cart.remove') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $item['product']->id }}">
                                <button type="submit" class="text-red-400 hover:text-red-600 text-xs transition">Hapus</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm p-6 sticky top-36">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Ringkasan Pesanan</h2>
                    <div class="space-y-2 mb-4">
                        @foreach($cartItems as $item)
                            <div class="flex justify-between text-sm text-gray-600">
                                <span class="truncate pr-2">{{ $item['product']->name }} ×{{ $item['quantity'] }}</span>
                                <span class="shrink-0">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="border-t pt-4 mb-6">
                        <div class="flex justify-between font-bold text-gray-800">
                            <span>Total</span>
                            <span class="text-orange-600 text-lg">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <form action="{{ route('cart.checkout') }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3 rounded-lg font-semibold transition">
                            Checkout Sekarang
                        </button>
                    </form>
                    <a href="{{ route('home') }}"
                       class="block text-center text-sm text-gray-500 hover:text-orange-600 mt-3 transition">
                        ← Lanjutkan Belanja
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection