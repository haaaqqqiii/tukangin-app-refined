<?php

// tests/Unit/CheckoutTest.php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

// Path 1 — Keranjang kosong
test('checkout gagal jika keranjang session kosong', function () {
    $user = User::factory()->create(['is_admin' => false]);

    $response = $this->actingAs($user)->post('/checkout');

    $response->assertRedirect();
    $response->assertSessionHas('error', 'Keranjang Anda kosong.');
    $this->assertDatabaseCount('orders', 0);
});

// Path 2 — Produk tidak ditemukan di database
test('checkout gagal jika product_id di session tidak ada di database', function () {
    $user = User::factory()->create(['is_admin' => false]);

    // Menyisipkan ID produk yang tidak ada (ID: 9999)
    session(['cart' => [9999 => 2]]);

    $response = $this->actingAs($user)
        ->withSession(['cart' => [9999 => 2]])
        ->post('/checkout');

    $response->assertRedirect();
    $response->assertSessionHas('error', 'Produk tidak ditemukan.');
    $this->assertDatabaseCount('orders', 0);
});

// Path 3 — Kuantitas melebihi stok
test('checkout gagal jika kuantitas melebihi stok produk', function () {
    $user    = User::factory()->create(['is_admin' => false]);
    $product = Product::factory()->create(['stock' => 5, 'price' => 10000]);

    $response = $this->actingAs($user)
        ->withSession(['cart' => [$product->id => 10]]) // qty 10 > stok 5
        ->post('/checkout');

    $response->assertRedirect();
    $response->assertSessionHas('error');
    // Verifikasi stok tidak berubah
    $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 5]);
    $this->assertDatabaseCount('orders', 0);
});

// Path 4 — Checkout berhasil penuh
test('checkout berhasil membuat order dan mengurangi stok', function () {
    $user    = User::factory()->create(['is_admin' => false]);
    $product = Product::factory()->create([
        'stock' => 10,
        'price' => 65000,
        'name'  => 'Semen Portland 50kg',
    ]);

    $response = $this->actingAs($user)
        ->withSession(['cart' => [$product->id => 3]]) // qty 3 ≤ stok 10
        ->post('/checkout');

    $response->assertRedirect(route('orders.index'));
    $response->assertSessionHas('success');

    // Verifikasi order terbuat
    $this->assertDatabaseHas('orders', [
        'user_id'     => $user->id,
        'total_price' => 65000 * 3,
        'status'      => 'pending',
    ]);

    // Verifikasi order_items terbuat
    $order = Order::where('user_id', $user->id)->first();
    $this->assertDatabaseHas('order_items', [
        'order_id'   => $order->id,
        'product_id' => $product->id,
        'quantity'   => 3,
        'price'      => 65000,
    ]);

    // Verifikasi stok berkurang
    $this->assertDatabaseHas('products', [
        'id'    => $product->id,
        'stock' => 7, // 10 - 3
    ]);
});
