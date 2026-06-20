<?php

// tests/Feature/AdminProductTest.php

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Helper
function adminUser() {
    return User::factory()->create(['is_admin' => true]);
}

function regularUser() {
    return User::factory()->create(['is_admin' => false]);
}

// EC1 — Tambah produk valid
test('admin dapat menambahkan produk baru dengan data lengkap', function () {
    $response = $this->actingAs(adminUser())->post('/admin/products', [
        'name'        => 'Semen Tiga Roda 50kg',
        'category'    => 'Semen',
        'price'       => 68000,
        'unit'        => 'sak',
        'image'       => 'https://images.unsplash.com/photo-test.jpg',
        'description' => 'Semen berkualitas tinggi untuk konstruksi.',
        'stock'       => 100,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Produk berhasil ditambahkan.');
    $this->assertDatabaseHas('products', ['name' => 'Semen Tiga Roda 50kg']);
});

// EC2 — Field name kosong
test('admin tidak dapat menambahkan produk tanpa nama', function () {
    $response = $this->actingAs(adminUser())->post('/admin/products', [
        'name'        => '',
        'category'    => 'Semen',
        'price'       => 68000,
        'unit'        => 'sak',
        'image'       => 'https://images.unsplash.com/photo-test.jpg',
        'description' => 'Deskripsi produk.',
        'stock'       => 100,
    ]);

    $response->assertSessionHasErrors(['name']);
    $this->assertDatabaseCount('products', 0);
});

// EC4 — URL gambar tidak valid
test('admin tidak dapat menambahkan produk dengan URL gambar tidak valid', function () {
    $response = $this->actingAs(adminUser())->post('/admin/products', [
        'name'        => 'Produk Test',
        'category'    => 'Semen',
        'price'       => 68000,
        'unit'        => 'sak',
        'image'       => 'bukan-url-valid',
        'description' => 'Deskripsi produk.',
        'stock'       => 100,
    ]);

    $response->assertSessionHasErrors(['image']);
});

// EC5 — Stok negatif (BVA: -1)
test('admin tidak dapat menambahkan produk dengan stok negatif', function () {
    $response = $this->actingAs(adminUser())->post('/admin/products', [
        'name'        => 'Produk Test',
        'category'    => 'Semen',
        'price'       => 68000,
        'unit'        => 'sak',
        'image'       => 'https://images.unsplash.com/photo-test.jpg',
        'description' => 'Deskripsi produk.',
        'stock'       => -1,
    ]);

    $response->assertSessionHasErrors(['stock']);
});

// EC6 — User biasa tidak dapat akses halaman admin
test('user biasa diblokir middleware saat mengakses halaman admin produk', function () {
    $response = $this->actingAs(regularUser())->get('/admin/products');

    $response->assertStatus(403);
});

// Update produk
test('admin dapat memperbarui data produk yang sudah ada', function () {
    $product = Product::factory()->create(['name' => 'Produk Lama', 'price' => 50000]);

    $response = $this->actingAs(adminUser())->put("/admin/products/{$product->id}", [
        'name'        => 'Produk Diperbarui',
        'category'    => 'Semen',
        'price'       => 75000,
        'unit'        => 'sak',
        'image'       => 'https://images.unsplash.com/photo-test.jpg',
        'description' => 'Deskripsi baru.',
        'stock'       => 200,
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Produk Diperbarui']);
});

// Hapus produk
test('admin dapat menghapus produk dari database', function () {
    $product = Product::factory()->create();

    $response = $this->actingAs(adminUser())->delete("/admin/products/{$product->id}");

    $response->assertRedirect();
    $this->assertDatabaseMissing('products', ['id' => $product->id]);
});
