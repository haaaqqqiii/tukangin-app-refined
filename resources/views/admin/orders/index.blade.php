@extends('layouts.admin')
@section('title', 'Kelola Pesanan')
@section('content')

<h2 class="text-2xl font-bold text-gray-800 mb-6">Kelola Pesanan</h2>

@php
    $statusMap = [
        'pending'    => ['label' => 'Menunggu',  'class' => 'bg-yellow-100 text-yellow-700'],
        'processing' => ['label' => 'Diproses',  'class' => 'bg-blue-100 text-blue-700'],
        'shipped'    => ['label' => 'Dikirim',   'class' => 'bg-green-100 text-green-700'],
        'rejected'   => ['label' => 'Ditolak',   'class' => 'bg-red-100 text-red-700'],
    ];
@endphp

@if($orders->isEmpty())
    <div class="bg-white rounded-xl shadow-sm p-16 text-center">
        <p class="text-gray-500 text-lg">Belum ada pesanan</p>
    </div>
@else
    <div class="space-y-5">
        @foreach($orders as $order)
            @php $s = $statusMap[$order->status]; @endphp
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="font-semibold text-gray-800">Pesanan #{{ $order->id }}</h3>
                        <p class="text-sm text-gray-500">Pelanggan: {{ $order->user->name }}</p>
                        <p class="text-xs text-gray-400">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $s['class'] }}">{{ $s['label'] }}</span>
                </div>
                <div class="border-t border-b py-4 mb-4 space-y-3">
                    @foreach($order->items as $item)
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center gap-3">
                                <img src="{{ $item->product->image }}" alt="{{ $item->product->name }}"
                                     class="w-10 h-10 object-cover rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-800">{{ $item->product->name }}</p>
                                    <p class="text-xs text-gray-400">
                                        {{ $item->quantity }} {{ $item->product->unit }} × Rp {{ number_format($item->price, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                            <p class="font-semibold text-orange-600">
                                Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                            </p>
                        </div>
                    @endforeach
                </div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500">Total Pembayaran</p>
                        <p class="text-xl font-bold text-orange-600">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                    </div>
                    <div class="flex gap-2">
                        @if($order->status === 'pending')
                            <form action="{{ route('admin.orders.status', $order) }}" method="POST">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="processing">
                                <button type="submit" class="flex items-center gap-1.5 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm transition">
                                    Proses
                                </button>
                            </form>
                            <form action="{{ route('admin.orders.status', $order) }}" method="POST"
                                  onsubmit="return confirm('Yakin ingin menolak pesanan ini?')">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="flex items-center gap-1.5 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm transition">
                                    Tolak
                                </button>
                            </form>
                        @elseif($order->status === 'processing')
                            <form action="{{ route('admin.orders.status', $order) }}" method="POST">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="shipped">
                                <button type="submit" class="flex items-center gap-1.5 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm transition">
                                    Kirim
                                </button>
                            </form>
                        @else
                            <span class="text-sm text-gray-400 italic">Selesai</span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection