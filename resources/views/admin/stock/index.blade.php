@extends('layouts.admin')
@section('title', 'Update Stok')
@section('content')

<h2 class="text-2xl font-bold text-gray-800 mb-6">Update Stok</h2>

@php $lowStock = $products->where('stock', '<', 10); @endphp
@if($lowStock->count() > 0)
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
        <p class="font-medium text-red-800">⚠️ {{ $lowStock->count() }} produk memiliki stok rendah (< 10)</p>
    </div>
@endif

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-600 font-medium">Produk</th>
                    <th class="px-4 py-3 text-left text-gray-600 font-medium">Kategori</th>
                    <th class="px-4 py-3 text-left text-gray-600 font-medium">Stok Saat Ini</th>
                    <th class="px-4 py-3 text-left text-gray-600 font-medium">Update Stok</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($products as $product)
                    <tr class="hover:bg-gray-50 {{ $product->stock < 10 ? 'bg-red-50' : '' }}">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $product->image }}" alt="{{ $product->name }}"
                                     class="w-10 h-10 object-cover rounded-lg">
                                <span class="font-medium text-gray-800">{{ $product->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="bg-orange-100 text-orange-600 px-2 py-0.5 rounded text-xs">{{ $product->category }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-semibold {{ $product->stock < 10 ? 'text-red-600' : 'text-green-600' }}">
                                {{ $product->stock }}
                            </span>
                            <span class="text-gray-400">{{ $product->unit }}</span>
                            @if($product->stock < 10)
                                <span class="text-xs text-red-500 ml-1">⚠ Rendah</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <form action="{{ route('admin.stock.update', $product) }}" method="POST" class="flex items-center gap-2">
                                @csrf @method('PATCH')
                                <input type="number" name="stock" value="{{ $product->stock }}" min="0" required
                                       class="w-24 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                                <button type="submit"
                                        class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1.5 rounded-lg text-sm transition">
                                    Simpan
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection