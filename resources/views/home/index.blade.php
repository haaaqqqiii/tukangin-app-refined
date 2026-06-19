@extends('layouts.app')
@section('title', 'Tukangin - Toko Bahan Bangunan Online')
@section('content')

<section class="bg-gradient-to-r from-orange-500 to-orange-600 text-white py-12 md:py-16">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-3xl md:text-4xl font-bold mb-4">Belanja Bahan Bangunan Online</h1>
        <p class="text-lg text-orange-100 mb-6">Harga terbaik, kualitas terjamin, pengiriman cepat</p>
        @guest
            <a href="{{ route('login') }}"
               class="inline-block bg-white text-orange-600 font-semibold px-8 py-3 rounded-lg hover:bg-gray-100 transition">
                Mulai Belanja Sekarang
            </a>
        @endguest
    </div>
</section>

<div x-data="homeApp()" class="container mx-auto px-4 py-8">

    <div class="flex gap-2 overflow-x-auto pb-2 mb-8">
        <button @click="selectedCategory = 'Semua'"
                :class="selectedCategory === 'Semua' ? 'bg-orange-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                class="px-4 py-2 rounded-lg whitespace-nowrap text-sm transition border border-gray-200">Semua</button>
        <template x-for="cat in categories" :key="cat">
            <button @click="selectedCategory = cat"
                    :class="selectedCategory === cat ? 'bg-orange-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                    class="px-4 py-2 rounded-lg whitespace-nowrap text-sm transition border border-gray-200"
                    x-text="cat"></button>
        </template>
    </div>

    <h2 class="text-2xl font-semibold mb-6"
        x-text="selectedCategory === 'Semua' ? 'Semua Produk' : selectedCategory"></h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <template x-for="product in filteredProducts" :key="product.id">
            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition overflow-hidden border border-gray-100">
                <div class="relative">
                    <img :src="product.image" :alt="product.name" class="w-full h-48 object-cover">
                    <span class="absolute top-2 left-2 bg-orange-500 text-white text-xs px-2 py-1 rounded"
                          x-text="product.category"></span>
                </div>
                <div class="p-4">
                    <h3 class="font-semibold text-gray-800 mb-1 line-clamp-2" x-text="product.name"></h3>
                    <p class="text-orange-600 font-bold text-lg mb-1">
                        <span x-text="formatPrice(product.price)"></span>
                        <span class="text-gray-400 font-normal text-sm">/<span x-text="product.unit"></span></span>
                    </p>
                    <p class="text-xs mb-3" :class="product.stock < 10 ? 'text-red-500' : 'text-green-600'">
                        Stok: <span x-text="product.stock"></span> <span x-text="product.unit"></span>
                    </p>
                    <div class="flex gap-2">
                        <button @click="openDetail(product)"
                                class="flex-1 border border-orange-500 text-orange-500 hover:bg-orange-50 py-1.5 rounded text-sm transition">
                            Lihat Detail
                        </button>
                        @auth
                            <button @click="quickAdd(product)"
                                    :disabled="product.stock === 0"
                                    :class="product.stock === 0 ? 'bg-gray-300 cursor-not-allowed' : 'bg-orange-500 hover:bg-orange-600'"
                                    class="flex-1 text-white py-1.5 rounded text-sm transition">
                                + Keranjang
                            </button>
                        @else
                            <a href="{{ route('login') }}"
                               class="flex-1 bg-orange-500 hover:bg-orange-600 text-white py-1.5 rounded text-sm text-center transition">
                                + Keranjang
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </template>
        <template x-if="filteredProducts.length === 0">
            <div class="col-span-full text-center py-16 text-gray-400">
                <p class="text-lg">Tidak ada produk dalam kategori ini.</p>
            </div>
        </template>
    </div>

    {{-- Modal Detail --}}
    <div x-show="selectedProduct" x-cloak @click.self="selectedProduct = null"
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div x-show="selectedProduct"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-2xl max-w-lg w-full overflow-hidden shadow-2xl">
            <template x-if="selectedProduct">
                <div>
                    <img :src="selectedProduct.image" :alt="selectedProduct.name" class="w-full h-64 object-cover">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-2">
                            <h3 class="text-xl font-bold text-gray-800" x-text="selectedProduct.name"></h3>
                            <button @click="selectedProduct = null" class="text-gray-400 hover:text-gray-600 ml-2">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <span class="inline-block bg-orange-100 text-orange-600 text-xs px-2 py-1 rounded mb-3"
                              x-text="selectedProduct.category"></span>
                        <p class="text-gray-600 text-sm mb-4" x-text="selectedProduct.description"></p>
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-2xl font-bold text-orange-600" x-text="formatPrice(selectedProduct.price)"></p>
                                <p class="text-sm text-gray-400">per <span x-text="selectedProduct.unit"></span></p>
                            </div>
                            <p class="text-sm" :class="selectedProduct.stock < 10 ? 'text-red-500' : 'text-green-600'">
                                Stok: <span x-text="selectedProduct.stock"></span>
                            </p>
                        </div>
                        @auth
                            <form :action="'{{ route('cart.add') }}'" method="POST" class="flex gap-3">
                                @csrf
                                <input type="hidden" name="product_id" :value="selectedProduct.id">
                                <input type="number" name="quantity" x-model="detailQty" min="1"
                                       :max="selectedProduct.stock"
                                       class="w-24 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                                <button type="submit" :disabled="selectedProduct.stock === 0"
                                        class="flex-1 bg-orange-500 hover:bg-orange-600 text-white py-2 rounded-lg text-sm font-medium transition disabled:bg-gray-300">
                                    Tambah ke Keranjang
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}"
                               class="block w-full bg-orange-500 hover:bg-orange-600 text-white py-2 rounded-lg text-sm font-medium text-center transition">
                                Login untuk Membeli
                            </a>
                        @endauth
                    </div>
                </div>
            </template>
        </div>
    </div>

    @auth
    <form id="quickAddForm" action="{{ route('cart.add') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="product_id" id="quickAddProductId">
        <input type="hidden" name="quantity" value="1">
    </form>
    @endauth

</div>
@endsection

@push('scripts')
<script>
function homeApp() {
    return {
        categories: @json($categories),
        allProducts: @json($products),
        selectedCategory: 'Semua',
        selectedProduct: null,
        detailQty: 1,
        get filteredProducts() {
            if (this.selectedCategory === 'Semua') return this.allProducts;
            return this.allProducts.filter(p => p.category === this.selectedCategory);
        },
        openDetail(product) { this.selectedProduct = product; this.detailQty = 1; },
        quickAdd(product) {
            if (product.stock === 0) return;
            document.getElementById('quickAddProductId').value = product.id;
            document.getElementById('quickAddForm').submit();
        },
        formatPrice(price) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(price);
        }
    }
}
</script>
@endpush