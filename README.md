# Tukangin — Toko Bahan Bangunan Online

Aplikasi web e-commerce bahan bangunan berbasis **Laravel + Blade + Sanctum** dengan arsitektur monolith. Memiliki dua mode akses: halaman pelanggan (browse produk, keranjang, checkout) dan dashboard admin (kelola produk, pesanan, stok).

---

## Daftar Isi

1. [Teknologi yang Digunakan](#teknologi-yang-digunakan)
2. [Fitur Aplikasi](#fitur-aplikasi)
3. [Prasyarat](#prasyarat)
4. [Instalasi & Setup](#instalasi--setup)
5. [Konfigurasi .env](#konfigurasi-env)
6. [Struktur File Proyek](#struktur-file-proyek)
7. [Struktur Database](#struktur-database)
8. [Panduan Penggunaan — Pelanggan](#panduan-penggunaan--pelanggan)
9. [Panduan Penggunaan — Admin](#panduan-penggunaan--admin)
10. [Akun Default](#akun-default)
11. [Mengatasi Error IDE (VS Code)](#mengatasi-error-ide-vs-code)
12. [Troubleshooting](#troubleshooting)

---

## Teknologi yang Digunakan

| Teknologi | Versi | Peran |
|---|---|---|
| **Laravel** | 11+ | Framework PHP monolith |
| **Laravel Sanctum** | latest | Autentikasi session-based (siap API token) |
| **Blade** | bawaan Laravel | Templating engine server-side |
| **Tailwind CSS** | v3 (CDN) | Styling UI |
| **Alpine.js** | v3 (CDN) | Interaktivitas ringan (modal, filter, form) |
| **MySQL / SQLite** | — | Database relasional |
| **PHP** | >= 8.2 | Bahasa pemrograman backend |

> Tail
wind CSS dan Alpine.js digunakan via CDN sehingga **tidak memerlukan Node.js atau build step**.

---

## Fitur Aplikasi

### Pelanggan
- Browse produk dengan filter kategori (client-side, tanpa reload halaman)
- Modal detail produk (gambar, deskripsi, harga, stok)
- Keranjang belanja berbasis session PHP
- Update jumlah / hapus item keranjang
- Checkout & buat pesanan
- Riwayat pesanan pribadi dengan status realtime

### Admin
- Dashboard statistik (total produk, pesanan pending, total pendapatan, peringatan stok rendah)
- Kelola produk: tambah, edit, hapus (modal Alpine.js)
- Kelola pesanan: proses → kirim → tolak
- Update stok produk

### Sistem Login
- Satu form login untuk admin dan pelanggan
- Deteksi otomatis: jika `is_admin = true` → redirect ke `/admin`, selain itu → redirect ke `/`
- Fitur daftar akun baru untuk pelanggan
- Proteksi halaman admin dengan middleware `AdminMiddleware`

---

## Prasyarat

Pastikan sudah terinstal di komputer Anda:

- **PHP** >= 8.2 → cek: `php -v`
- **Composer** >= 2.x → cek: `composer -V`
- **MySQL** (via XAMPP / Laragon / standalone) → cek: `mysql -V`
- **Git** (opsional) → cek: `git -v`

> **Re
komendasi untuk Windows:** Gunakan [Laragon](https://laragon.org/) karena sudah menyertakan PHP, MySQL, dan Composer sekaligus.

---

## Instalasi & Setup

### Langkah 1 — Buat Proyek Laravel Baru

Buka terminal / command prompt, lalu jalankan:

```bash
composer create-project laravel/laravel tukangin-app-refined
cd tukangin-app-refined
```

### Langkah 2 — Install Laravel Sanctum

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

### Langkah 3 — Salin Semua File dari Repositori Ini

Salin file-file berikut ke dalam folder Laravel yang baru dibuat:

```
Salin dari repo ini → ke dalam folder tukangin-app-refined/
─────────────────────────────────────────────────────────────────
routes/web.php                          → routes/web.php
app/Http/Middleware/AdminMiddleware.php → app/Http/Middleware/AdminMiddleware.php
app/Http/Controllers/                  → app/Http/Controllers/
app/Models/                            → app/Models/
database/migrations/                   → database/migrations/
database/seeders/                      → database/seeders/
resources/views/                       → resources/views/
```

> **Perhatian:** Timpa file yang sudah ada
 (terutama `app/Models/User.php` dan migration `create_users_table`).

### Langkah 4 — Daftarkan Middleware Admin

Buka file `bootstrap/app.php` dan tambahkan alias middleware `admin`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
    ]);
})
```

Contoh lengkap `bootstrap/app.php`:

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
```

> **Laravel 10 
ke bawah:** Tambahkan di `app/Http/Kernel.php` pada array `$routeMiddleware`:
> ```php
> 'admin' => \App\Http\Middleware\AdminMiddleware::class,
> ```

### Langkah 5 — Konfigurasi File .env

```bash
cp .env.example .env
```

Edit `.env` sesuai panduan di bagian [Konfigurasi .env](#konfigurasi-env).

### Langkah 6 — Generate Application Key

```bash
php artisan key:generate
```

### Langkah 7 — Buat Database

**Menggunakan MySQL:**

```sql
CREATE DATABASE db_tukangin_app_refined CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Menggunakan SQLite (lebih mudah untuk development):**

```bash
touch database/database.sqlite
```

Lalu ubah `DB_CONNECTION=sqlite` di `.env`.

### Langkah 8 — Jalankan Migrasi dan Seeder

```bash
php artisan migrate:fresh --seed
```

Perintah ini akan:
- Membuat semua tabel database (users, products, orders, order_items, sessions)
- Mengisi data awal: akun admin, akun demo user, dan 8 produk bahan bangunan

### Langkah 9 — Jalankan Server

```bash
php artisan serve
```

Buka browser dan akses: **http://localhost:8000**

---

## Konfigurasi .env

```env
APP_NAME=Tukangin
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

APP_LOCALE=id
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=id_ID

LOG_CHANNEL=stack
LOG_LEVEL=debug

# ─── MySQL (default) ───────────────────────────────────────
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_tukangin_app_refined
DB_USERNAME=root
DB_PASSWORD=

# ─── SQLite (uncomment jika ingin pakai SQLite) ────────────
# DB_CONNECTION=sqlite

# ─── Session (wajib 'database' agar keranjang berfungsi) ───
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

# ─── Cache & Queue ─────────────────────────────────────────
CACHE_STORE=database
QUEUE_CONNECTION=database

# ─── Mail ──────────────────────────────────────────────────
MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@tukangin.com"
MAIL_FROM_NAME="${APP_NAME}"
```

> **Penting:** `SESSION_DRIVER=database` wajib 
agar keranjang belanja berfungsi dengan benar.

---

## Struktur File Proyek

```
tukangin-app-refined/
│
├── routes/
│   └── web.php                             ← Semua route (auth, user, admin)
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   └── LoginController.php     ← Login, register, logout
│   │   │   ├── HomeController.php          ← Halaman beranda + produk
│   │   │   ├── CartController.php          ← Keranjang berbasis session
│   │   │   ├── OrderController.php         ← Riwayat pesanan user
│   │   │   └── Admin/
│   │   │       ├── DashboardController.php ← Statistik admin
│   │   │       ├── ProductController.php   ← CRUD produk
│   │   │       ├── OrderController.php     ← Kelola status pesanan
│   │   │       └── StockController.php     ← Update stok
│   │   └── Middleware/
│   │       └── AdminMiddleware.php         ← Proteksi route admin
│   └── Models/
│       ├── User.php                        ← HasApiTokens (Sanctum)
│       ├── Product.php
│       ├── Order.php
│       └── OrderItem.php
│
├── database/
│   ├── migrations/
│   │   ├── ..._create_users_table.php
│   │   ├── ..._create_products_table.php
│   │   ├── ..._create_orders_table.php
│   │   └── ..._create_order_items_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── UserSeeder.php                  ← Admin + demo user
│       └── ProductSeeder.php               ← 8 produk awal
│
└── resources/
    └── views/
        ├── layouts/
        │   ├── app.blade.php               ← Layout halaman pelanggan
        │   └── admin.blade.php             ← Layout dashboard admin
        ├── auth/
        │   └── login.blade.php             ← Form login & daftar
        ├── home/
        │   └── index.blade.php             ← Beranda + grid produk
        ├── cart/
        │   └── index.blade.php             ← Halaman keranjang
        ├── orders/
        │   └── index.blade.php             ← Riwayat pesanan
        └── admin/
            ├── dashboard.blade.php         ← Statistik & pesanan terbaru
            ├── products/
            │   └── index.blade.php         ← Tabel produk + modal CRUD
            ├── orders/
            │   └── index.blade.php         ← Daftar & kelola pesanan
            └── stock/
                └── index.blade.php         ← Update stok per produk
```

---

## Struktur Database

### Tabel `users`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | Primary key |
| name | varchar | Nama pengguna |
| email | varchar | Email (unique) |
| password | varchar | Password ter-hash (bcrypt) |
| is_admin | boolean | `false` = pelanggan, `true` = admin |
| remember_token | varchar | Token "ingat saya" |
| created_at / updated_at | timestamp | — |

### Tabel `products`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | Primary key |
| name | varchar | Nama produk |
| category | varchar | Kategori (Semen, Cat, Bata, dll) |
| price | bigint | Harga dalam Rupiah |
| unit | varchar | Satuan (sak, batang, biji, m², dll) |
| image | text | URL gambar produk |
| description | text | Deskripsi produk |
| stock | int | Jumlah stok tersedia |

### Tabel `orders`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | Primary key |
| user_id | bigint | FK → users.id |
| total_price | bigint | Total harga pesanan (Rupiah) |
| status | enum | `pending`, `processing`, `shipped`, `rejected` |

### Tabel `order_items`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint | Primary key |
| order_id | bigint | FK → orders.id |
| product_id | bigint | FK → products.id |
| quantity | int | Jumlah item yang dipesan |
| price | bigint | Harga satuan saat checkout (snapshot) |

### Relasi Antar Tabel

```
users ──< orders ──< order_items >── products
```

---

## Panduan Penggunaan — Pelanggan

### 1. Membuka Aplikasi

Buka browser dan akses `http://localhost:8000`.

### 2. Mendaftar Akun

1. Klik **"Masuk / Daftar"** di pojok kanan atas
2. Pilih tab **"Daftar"**
3. Isi Nama Lengkap, Email, Password, dan Konfirmasi Password
4. Klik **"Buat Akun"** — otomatis login dan diarahkan ke beranda

### 3. Login

1. Klik **"Masuk / Daftar"**
2. Masukkan email dan password
3. Klik **"Masuk"**

### 4. Browse & Filter Produk

- Klik tombol kategori (Semen, Cat, Bata, dll) untuk filter produk secara langsung
- Klik **"Lihat Detail"** untuk melihat deskripsi lengkap, harga, dan stok

### 5. Menambah ke Keranjang

- **Cara cepat:** klik **"+ Keranjang"** langsung dari kartu produk (tambah 1 item)
- **Dari modal detail:** atur jumlah → klik **"Tambah ke Keranjang"**

### 6. Mengelola Keranjang

1. Klik ikon keranjang di header
2. Update jumlah: ubah angka → klik **"OK"**
3. Hapus item: klik **"Hapus"**

### 7. Checkout

1. Di halaman keranjang, klik **"Checkout Sekarang"**
2. Sistem validasi stok otomatis
3. Jika berhasil → pesanan dibuat, stok berkurang, diarahkan ke riwayat pesanan

### 8. Riwayat Pesanan

Klik **"Pesanan Saya"** di header untuk melihat semua pesanan beserta statusnya:

| Status | Keterangan |
|---|---|
| 🟡 Menunggu | Pesanan baru masuk |
| 🔵 Diproses | Admin sedang memproses |
| 🟢 Dikirim | Pesanan dalam pengiriman |
| 🔴 Ditolak | Pesanan ditolak admin |

### 9. Logout

Klik **"Keluar"** di header. Session dan keranjang akan dihapus.

---

## Panduan Penggunaan — Admin

### 1. Login Admin

- **Email:** `admin@tukangin.com`
- **Password:** `admin123`
- Sistem otomatis redirect ke `/admin`

### 2. Dashboard

Menampilkan kartu statistik: Total Produk, Pesanan Pending, Total Pendapatan dari pesanan terkirim, dan banner peringatan jika ada stok < 10.

### 3. Kelola Produk (`/admin/products`)

| Aksi | Cara |
|---|---|
| **Tambah** | Klik "Tambah Produk" → isi form modal → klik "Tambah Produk" |
| **Edit** | Klik ikon pensil pada baris produk → ubah data → klik "Perbarui Produk" |
| **Hapus** | Klik ikon tempat sampah → konfirmasi dialog |

### 4. Kelola Pesanan (`/admin/orders`)

Alur status pesanan:

```
[Menunggu] ──→ klik "Proses" ──→ [Diproses] ──→ klik "Kirim" ──→ [Dikirim]
[Menunggu] ──→ klik "Tolak"  ──→ [Ditolak]
```

### 5. Update Stok (`/admin/stock`)

- Produk diurutkan dari stok terendah
- Produk dengan stok < 10 ditandai latar merah
- Ubah angka pada kolom "Update Stok" → klik **"Simpan"**

### 6. Logout

Klik tombol **"Keluar"** (merah) di pojok kanan atas.

---

## Akun Default

| Role | Email | Password | Akses |
|---|---|---|---|
| **Admin** | admin@tukangin.com | admin123 | `/admin` |
| **User Demo** | user@tukangin.com | password | `/` |

---

## Mengatasi Error IDE (VS Code)

Warning seperti `"Undefined method 'check'"` dari Intelephense adalah **false positive** — kode tetap berjalan normal. Penyebabnya: IDE tidak mengenali helper `auth()` dan magic method Eloquent.

### Solusi 1 — Ganti `auth()` dengan `Auth::` Facade

```php
// Tambahkan use statement di atas class
use Illuminate\Support\Facades\Auth;

// Ganti
auth()->check()  →  Auth::check()
auth()->id()     →  Auth::id()
auth()->user()   →  Auth::user()
```

### Solusi 2 — Install Laravel IDE Helper (solusi menyeluruh)

```bash
composer require --dev barryvdh/laravel-ide-helper
php artisan ide-helper:generate
php artisan ide-helper:models --nowrite
```

Restart VS Code — semua warning Eloquent akan hilang.

---

## Troubleshooting

### ❌ `Class 'AdminMiddleware' not found`
Pastikan sudah menambahkan alias di `bootstrap/app.php`:
```php
$middleware->alias(['admin' => \App\Http\Middleware\AdminMiddleware::class]);
```

### ❌ `Unknown database 'tukangin'`
Buat database terlebih dahulu:
```sql
CREATE DATABASE db_tukangin_app_refined;
```

### ❌ `419 Page Expired` saat submit form
```bash
php artisan config:clear
php artisan cache:clear
```

### ❌ Keranjang selalu kosong setelah refresh
Pastikan di `.env`:
```env
SESSION_DRIVER=database
```
Lalu jalankan `php artisan migrate`.

### ❌ Halaman `/admin` error 403
Login menggunakan `admin@tukangin.com` / `admin123`.

### ❌ Produk tidak muncul di beranda
```bash
php artisan db:seed --class=ProductSeeder
```

---

## Perintah Artisan yang Sering Digunakan

```bash
# Jalankan server development
php artisan serve

# Reset database + isi ulang data awal
php artisan migrate:fresh --seed

# Hanya jalankan seeder (tanpa reset tabel)
php artisan db:seed

# Bersihkan semua cache
php artisan optimize:clear

# Lihat semua route yang terdaftar
php artisan route:list
```

---

## Lisensi

Proyek ini dibuat untuk keperluan pembelajaran. Bebas digunakan dan dimodifikasi.