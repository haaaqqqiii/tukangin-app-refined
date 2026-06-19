@extends('layouts.app')
@section('title', 'Pesanan Saya — Tukangin')
@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Pesanan Saya</h1>

    @if($orders->isEmpty())
        <div class="bg-white rounded-xl shadow-sm p-16 text-center">
            <svg class="w-20 h-20 mx-auto text-gray-200 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            <p class="text-gray-500 text-lg mb-4">Belum ada pesanan</p>
            <a href="{{ route('home') }}"
               class="inline-block bg-orange-500 hover:bg-orange-600 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition">
                Mulai Belanja
            </a>
        </div>
    @else
        <div class="space-y-5">
            @foreach($orders as $order)
                @php
                    $statusMap = [
                        'pending'    => ['label' => 'Menunggu',  'class' => 'bg-yellow-100 text-yellow-700'],
                        'processing' => ['label' => 'Diproses',  'class' => 'bg-blue-100 text-blue-700'],
                        'shipped'    => ['label' => 'Dikirim',   'class' => 'bg-green-100 text-green-700'],
                        'rejected'   => ['label' => 'Ditolak',   'class' => 'bg-red-100 text-red-700'],
                    ];
                    $status = $statusMap[$order->status];
                @endphp
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <p class="font-semibold text-gray-800">Pesanan #{{ $order->id }}</p>
                            <p class="text-xs text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $status['class'] }}">
                            {{ $status['label'] }}
                        </span>
                    </div>
                    <div class="border-t border-b py-4 mb-4 space-y-3">
                        @foreach($order->items as $item)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $item->product->image }}" alt="{{ $item->product->name }}"
                                         class="w-12 h-12 object-cover rounded-lg">
                                    <div>
                                        <p class="text-sm font-medium text-gray-800">{{ $item->product->name }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ $item->quantity }} {{ $item->product->unit }} × Rp {{ number_format($item->price, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                                <p class="text-sm font-semibold text-orange-600">
                                    Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex justify-end">
                        <div class="text-right">
                            <p class="text-xs text-gray-500">Total Pembayaran</p>
                            <p class="text-xl font-bold text-orange-600">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection