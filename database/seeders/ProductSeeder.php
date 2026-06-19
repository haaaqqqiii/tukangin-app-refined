<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name'        => 'Semen Portland 50kg',
                'category'    => 'Semen',
                'price'       => 65000,
                'unit'        => 'sak',
                'image'       => 'https://images.unsplash.com/photo-1523293915678-d126868e96f1?w=800&auto=format&fit=crop',
                'description' => 'Semen berkualitas tinggi untuk berbagai kebutuhan konstruksi bangunan.',
                'stock'       => 250,
            ],
            [
                'name'        => 'Besi Beton 10mm x 12m',
                'category'    => 'Besi & Baja',
                'price'       => 85000,
                'unit'        => 'batang',
                'image'       => 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=800&auto=format&fit=crop',
                'description' => 'Besi beton polos untuk struktur dan tulangan bangunan.',
                'stock'       => 500,
            ],
            [
                'name'        => 'Bata Merah Press',
                'category'    => 'Bata',
                'price'       => 850,
                'unit'        => 'biji',
                'image'       => 'https://images.unsplash.com/photo-1584505192555-4feb7834358a?w=800&auto=format&fit=crop',
                'description' => 'Bata merah berkualitas untuk dinding dan pagar.',
                'stock'       => 10000,
            ],
            [
                'name'        => 'Cat Tembok Premium 20L',
                'category'    => 'Cat',
                'price'       => 450000,
                'unit'        => 'pail',
                'image'       => 'https://images.unsplash.com/photo-1562259949-e8e7689d7828?w=800&auto=format&fit=crop',
                'description' => 'Cat tembok dengan daya tutup maksimal dan tahan lama.',
                'stock'       => 120,
            ],
            [
                'name'        => 'Keramik 40x40 cm',
                'category'    => 'Keramik',
                'price'       => 55000,
                'unit'        => 'm²',
                'image'       => 'https://images.unsplash.com/photo-1523350165414-082d792c4bcc?w=800&auto=format&fit=crop',
                'description' => 'Keramik lantai motif modern anti slip.',
                'stock'       => 800,
            ],
            [
                'name'        => 'Pasir Beton',
                'category'    => 'Pasir',
                'price'       => 350000,
                'unit'        => 'm³',
                'image'       => 'https://images.unsplash.com/photo-1605173983206-33cd0f25267e?w=800&auto=format&fit=crop',
                'description' => 'Pasir beton berkualitas untuk adukan cor.',
                'stock'       => 50,
            ],
            [
                'name'        => 'Genteng Keramik',
                'category'    => 'Genteng',
                'price'       => 12000,
                'unit'        => 'biji',
                'image'       => 'https://images.unsplash.com/photo-1518736346281-76873166a64a?w=800&auto=format&fit=crop',
                'description' => 'Genteng keramik tahan lama dan anti bocor.',
                'stock'       => 5000,
            ],
            [
                'name'        => 'Pipa PVC 3 inch',
                'category'    => 'Pipa',
                'price'       => 45000,
                'unit'        => 'batang',
                'image'       => 'https://images.unsplash.com/photo-1529269421632-e9253d14d3a9?w=800&auto=format&fit=crop',
                'description' => 'Pipa PVC untuk saluran air dan drainase.',
                'stock'       => 300,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}