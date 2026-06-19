<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tukangin - Toko Bahan Bangunan Online')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="min-h-screen bg-gray-50 flex flex-col">

    <header class="bg-white shadow-md sticky top-0 z-40">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center text-white font-bold text-lg">T</div>
                    <span class="text-xl font-bold text-orange-600">Tukangin</span>
                </a>
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('cart.index') }}" class="relative p-2 text-gray-600 hover:text-orange-600 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            @php $cartCount = array_sum(session('cart', [])); @endphp
                            @if($cartCount > 0)
                                <span class="absolute -top-1 -right-1 bg-orange-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                    {{ $cartCount }}
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('orders.index') }}" class="text-sm text-gray-600 hover:text-orange-600 hidden md:block transition">
                            Pesanan Saya
                        </a>
                        <span class="text-sm text-gray-700 hidden md:block">{{ auth()->user()->name }}</span>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-red-500 hover:text-red-700 transition">Keluar</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}"
                           class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm transition">
                            Masuk / Daftar
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-cloak
             x-init="setTimeout(() => show = false, 4000)"
             class="fixed top-20 left-1/2 -translate-x-1/2 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg text-sm">
            ✓ {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-cloak
             x-init="setTimeout(() => show = false, 5000)"
             class="fixed top-20 left-1/2 -translate-x-1/2 z-50 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg text-sm">
            ✕ {{ session('error') }}
        </div>
    @endif

    <main class="flex-1">@yield('content')</main>

    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="container mx-auto px-4 text-center">
            <h3 class="text-xl font-bold mb-2">Tukangin</h3>
            <p class="text-gray-400 mb-4">Toko bahan bangunan online terpercaya</p>
            <p class="text-sm text-gray-500">© {{ date('Y') }} Tukangin. Semua hak dilindungi.</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>