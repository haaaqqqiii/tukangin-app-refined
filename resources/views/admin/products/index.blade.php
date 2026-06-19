@extends('layouts.admin')
@section('title', 'Kelola Produk')
@section('content')

<div x-data="productManager()" x-cloak>
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Kelola Produk</h2>
        <button @click="openAdd()"
                class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm transition">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Produk
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-600 font-medium">Gambar</th>
                        <th class="px-4 py-3 text-left text-gray-600 font-medium">Nama</th>
                        <th class="px-4 py-3 text-left text-gray-600 font-medium">Kategori</th>
                        <th class="px-4 py-3 text-left text-gray-600 font-medium">Harga</th>
                        <th class="px-4 py-3 text-left text-gray-600 font-medium">Stok</th>
                        <th class="px-4 py-3 text-left text-gray-600 font-medium">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($products as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <img src="{{ $product->image }}" alt="{{ $product->name }}"
                                     class="w-12 h-12 object-cover rounded-lg">
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $product->name }}</td>
                            <td class="px-4 py-3">
                                <span class="bg-orange-100 text-orange-600 px-2 py-0.5 rounded text-xs">{{ $product->category }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-700">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                                <span class="text-gray-400">/{{ $product->unit }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="{{ $product->stock < 10 ? 'text-red-500 font-medium' : 'text-green-600' }}">
                                    {{ $product->stock }} {{ $product->unit }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-3">
                                    <button @click="openEdit({{ $product->toJson() }})" class="text-blue-500 hover:text-blue-700">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                          onsubmit="return confirm('Hapus produk {{ addslashes($product->name) }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-600">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal --}}
    <div x-show="showModal" x-cloak @click.self="showModal = false"
         class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div x-show="showModal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between p-6 border-b">
                <h3 class="text-lg font-bold" x-text="editProduct ? 'Edit Produk' : 'Tambah Produk Baru'"></h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form :action="editProduct ? '{{ url('admin/products') }}/' + editProduct.id : '{{ route('admin.products.store') }}'"
                  method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="_method" :value="editProduct ? 'PUT' : 'POST'">
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk</label>
                        <input type="text" name="name" x-model="form.name" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <input type="text" name="category" x-model="form.category" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp)</label>
                        <input type="number" name="price" x-model="form.price" min="0" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Satuan</label>
                        <input type="text" name="unit" x-model="form.unit" required placeholder="sak, batang, biji..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stok</label>
                        <input type="number" name="stock" x-model="form.stock" min="0" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">URL Gambar</label>
                        <input type="url" name="image" x-model="form.image" required placeholder="https://..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="description" x-model="form.description" rows="3" required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 resize-none"></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit"
                            class="flex-1 bg-orange-500 hover:bg-orange-600 text-white py-2.5 rounded-lg text-sm font-medium transition">
                        <span x-text="editProduct ? 'Perbarui Produk' : 'Tambah Produk'"></span>
                    </button>
                    <button type="button" @click="showModal = false"
                            class="px-6 border border-gray-300 hover:bg-gray-50 rounded-lg text-sm transition">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function productManager() {
    return {
        showModal: false,
        editProduct: null,
        form: { name: '', category: '', price: 0, unit: '', image: '', description: '', stock: 0 },
        openAdd() {
            this.editProduct = null;
            this.form = { name: '', category: '', price: 0, unit: '', image: '', description: '', stock: 0 };
            this.showModal = true;
        },
        openEdit(product) {
            this.editProduct = product;
            this.form = { name: product.name, category: product.category, price: product.price,
                          unit: product.unit, image: product.image, description: product.description, stock: product.stock };
            this.showModal = true;
        }
    }
}
</script>
@endpush