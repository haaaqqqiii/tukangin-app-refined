<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — Tukangin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="min-h-screen bg-gray-50">

    <header class="bg-white shadow-md sticky top-0 z-40">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center text-white font-bold text-lg">T</div>
                    <div>
                        <p class="text-base font-semibold leading-tight">Admin Dashboard</p>
                        <p class="text-xs text-gray-500">Tukangin</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right hidden md:block">
                        <p class="text-sm font-medium">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500">Administrator</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            <span class="hidden md:inline">Keluar</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    @php $route = request()->route()->getName(); @endphp
    <div class="bg-white border-b sticky top-[73px] z-30">
        <div class="container mx-auto px-4">
            <div class="flex gap-1 overflow-x-auto">
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-2 px-5 py-4 whitespace-nowrap text-sm transition
                          {{ $route === 'admin.dashboard' ? 'border-b-2 border-orange-500 text-orange-600 font-medium' : 'text-gray-600 hover:text-gray-900' }}">
                    Dashboard
                </a>
                <a href="{{ route('admin.products.index') }}"
                   class="flex items-center gap-2 px-5 py-4 whitespace-nowrap text-sm transition
                          {{ $route === 'admin.products.index' ? 'border-b-2 border-orange-500 text-orange-600 font-medium' : 'text-gray-600 hover:text-gray-900' }}">
                    Produk
                </a>
                <a href="{{ route('admin.orders.index') }}"
                   class="relative flex items-center gap-2 px-5 py-4 whitespace-nowrap text-sm transition
                          {{ $route === 'admin.orders.index' ? 'border-b-2 border-orange-500 text-orange-600 font-medium' : 'text-gray-600 hover:text-gray-900' }}">
                    Pesanan
                    @php $pending = \App\Models\Order::where('status','pending')->count(); @endphp
                    @if($pending > 0)
                        <span class="bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 leading-none">{{ $pending }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.stock.index') }}"
                   class="flex items-center gap-2 px-5 py-4 whitespace-nowrap text-sm transition
                          {{ $route === 'admin.stock.index' ? 'border-b-2 border-orange-500 text-orange-600 font-medium' : 'text-gray-600 hover:text-gray-900' }}">
                    Stok
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-cloak x-init="setTimeout(() => show = false, 4000)"
             class="fixed top-24 left-1/2 -translate-x-1/2 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg text-sm">
            ✓ {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-cloak x-init="setTimeout(() => show = false, 5000)"
             class="fixed top-24 left-1/2 -translate-x-1/2 z-50 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg text-sm">
            ✕ {{ session('error') }}
        </div>
    @endif

    <main class="container mx-auto px-4 py-8">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>