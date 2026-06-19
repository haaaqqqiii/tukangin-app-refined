@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')

<h2 class="text-2xl font-bold text-gray-800 mb-6">Dashboard Overview</h2>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-gray-500 text-sm">Total Produk</h3>
            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-gray-800">{{ $totalProducts }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-gray-500 text-sm">Pesanan Pending</h3>
            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-gray-800">{{ $pendingOrders }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-gray-500 text-sm">Total Pendapatan</h3>
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
        <p class="text-xs text-gray-400 mt-1">Dari pesanan terkirim</p>
    </div>
</div>

@if($lowStockCount > 0)
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 flex items-start gap-3">
        <span class="text-xl">⚠️</span>
        <div>
            <p class="font-medium text-red-800">Peringatan Stok Rendah</p>
            <p class="text-sm text-red-600">
                {{ $lowStockCount }} produk memiliki stok kurang dari 10.
                <a href="{{ route('admin.stock.index') }}" class="underline">Perbarui stok sekarang →</a>
            </p>
        </div>
    </div>
@endif

<div class="bg-white rounded-xl shadow-sm p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-gray-800">Pesanan Terbaru</h3>
        <a href="{{ route('admin.orders.index') }}" class="text-sm text-orange-500 hover:underline">Lihat semua →</a>
    </div>
    @if($recentOrders->isEmpty())
        <p class="text-gray-400 text-sm py-4 text-center">Belum ada pesanan.</p>
    @else
        @php
            $statusMap = [
                'pending'    => ['label' => 'Menunggu',  'class' => 'bg-yellow-100 text-yellow-700'],
                'processing' => ['label' => 'Diproses',  'class' => 'bg-blue-100 text-blue-700'],
                'shipped'    => ['label' => 'Dikirim',   'class' => 'bg-green-100 text-green-700'],
                'rejected'   => ['label' => 'Ditolak',   'class' => 'bg-red-100 text-red-700'],
            ];
        @endphp
        <div class="divide-y">
            @foreach($recentOrders as $order)
                @php $s = $statusMap[$order->status]; @endphp
                <div class="flex items-center justify-between py-3">
                    <div>
                        <p class="text-sm font-medium text-gray-800">Pesanan #{{ $order->id }}</p>
                        <p class="text-xs text-gray-500">{{ $order->user->name }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-orange-600">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                        <span class="text-xs px-2 py-0.5 rounded {{ $s['class'] }}">{{ $s['label'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@endsection