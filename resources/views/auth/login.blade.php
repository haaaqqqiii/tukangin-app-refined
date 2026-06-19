<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk / Daftar — Tukangin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-orange-50 to-orange-100 flex items-center justify-center p-4">

    <div x-data="{ mode: 'login' }" class="w-full max-w-md">

        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                <div class="w-14 h-14 bg-orange-500 rounded-2xl flex items-center justify-center text-white font-bold text-2xl shadow-lg">T</div>
                <span class="text-3xl font-bold text-orange-600">Tukangin</span>
            </a>
            <p class="text-gray-500 mt-2 text-sm">Toko bahan bangunan online terpercaya</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="flex bg-gray-100 rounded-lg p-1 mb-6">
                <button @click="mode = 'login'"
                        :class="mode === 'login' ? 'bg-white shadow text-orange-600 font-medium' : 'text-gray-500'"
                        class="flex-1 py-2 px-4 rounded-md text-sm transition">Masuk</button>
                <button @click="mode = 'register'"
                        :class="mode === 'register' ? 'bg-white shadow text-orange-600 font-medium' : 'text-gray-500'"
                        class="flex-1 py-2 px-4 rounded-md text-sm transition">Daftar</button>
            </div>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 rounded-lg px-4 py-3 mb-4 text-sm">
                    <ul class="space-y-1">
                        @foreach($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form x-show="mode === 'login'" action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="contoh@email.com"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" required placeholder="Masukkan password"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm">
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="remember" id="remember" class="rounded border-gray-300 text-orange-500">
                    <label for="remember" class="ml-2 text-sm text-gray-600">Ingat saya</label>
                </div>
                <button type="submit"
                        class="w-full bg-orange-500 hover:bg-orange-600 text-white py-2.5 rounded-lg font-medium transition text-sm">
                    Masuk
                </button>
                <p class="text-xs text-center text-gray-400">Admin: admin@tukangin.com / admin123</p>
            </form>

            <form x-show="mode === 'register'" action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Masukkan nama lengkap"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="contoh@email.com"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" required placeholder="Minimal 6 karakter"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required placeholder="Ulangi password"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-400 text-sm">
                </div>
                <button type="submit"
                        class="w-full bg-orange-500 hover:bg-orange-600 text-white py-2.5 rounded-lg font-medium transition text-sm">
                    Buat Akun
                </button>
            </form>
        </div>

        <p class="text-center mt-6">
            <a href="{{ route('home') }}" class="text-sm text-gray-500 hover:text-orange-600 transition">← Kembali ke Toko</a>
        </p>
    </div>

</body>
</html>