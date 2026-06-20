<?php

// tests/Feature/AuthTest.php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

// EP EC1 — Login valid sebagai user biasa
test('user biasa dapat login dan diarahkan ke halaman utama', function () {
    $user = User::factory()->create([
        'email'    => 'user@tukangin.com',
        'password' => Hash::make('password'),
        'is_admin' => false,
    ]);

    $response = $this->post('/login', [
        'email'    => 'user@tukangin.com',
        'password' => 'password',
    ]);

    $response->assertRedirect(route('home'));
    $this->assertAuthenticatedAs($user);
});

// EP EC1 (Admin variant) — Login valid sebagai admin
test('admin dapat login dan diarahkan ke dashboard admin', function () {
    $admin = User::factory()->create([
        'email'    => 'admin@tukangin.com',
        'password' => Hash::make('admin123'),
        'is_admin' => true,
    ]);

    $response = $this->post('/login', [
        'email'    => 'admin@tukangin.com',
        'password' => 'admin123',
    ]);

    $response->assertRedirect(route('admin.dashboard'));
    $this->assertAuthenticatedAs($admin);
});

// EP EC2 — Password salah
test('login gagal jika password tidak cocok', function () {
    User::factory()->create([
        'email'    => 'user@tukangin.com',
        'password' => Hash::make('password'),
    ]);

    $response = $this->post('/login', [
        'email'    => 'user@tukangin.com',
        'password' => 'passwordSalah',
    ]);

    $response->assertSessionHasErrors(['email']);
    $this->assertGuest();
});

// EP EC3 — Email tidak terdaftar
test('login gagal jika email tidak terdaftar', function () {
    $response = $this->post('/login', [
        'email'    => 'tidakada@tukangin.com',
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors(['email']);
    $this->assertGuest();
});

// EP EC4 — Format email tidak valid
test('login gagal jika format email tidak valid', function () {
    $response = $this->post('/login', [
        'email'    => 'bukanemailvalid',
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors(['email']);
});

// Register — EC1 Valid
test('user baru dapat mendaftar dengan data lengkap dan valid', function () {
    $response = $this->post('/register', [
        'name'                  => 'Pengguna Baru',
        'email'                 => 'baru@tukangin.com',
        'password'              => 'rahasia123',
        'password_confirmation' => 'rahasia123',
    ]);

    $response->assertRedirect(route('home'));
    $this->assertDatabaseHas('users', ['email' => 'baru@tukangin.com']);
});

// Register — EC2 Email duplikat
test('registrasi gagal jika email sudah terdaftar', function () {
    User::factory()->create(['email' => 'sudahada@tukangin.com']);

    $response = $this->post('/register', [
        'name'                  => 'Coba Lagi',
        'email'                 => 'sudahada@tukangin.com',
        'password'              => 'rahasia123',
        'password_confirmation' => 'rahasia123',
    ]);

    $response->assertSessionHasErrors(['email']);
});

// Register — EC3 Password terlalu pendek (BVA: 5 karakter)
test('registrasi gagal jika password kurang dari 6 karakter', function () {
    $response = $this->post('/register', [
        'name'                  => 'Pengguna Baru',
        'email'                 => 'baru2@tukangin.com',
        'password'              => 'abc12', // 5 karakter
        'password_confirmation' => 'abc12',
    ]);

    $response->assertSessionHasErrors(['password']);
});

// Register — EC4 Konfirmasi password tidak cocok
test('registrasi gagal jika konfirmasi password tidak cocok', function () {
    $response = $this->post('/register', [
        'name'                  => 'Pengguna Baru',
        'email'                 => 'baru3@tukangin.com',
        'password'              => 'rahasia123',
        'password_confirmation' => 'berbeda999',
    ]);

    $response->assertSessionHasErrors(['password']);
});
