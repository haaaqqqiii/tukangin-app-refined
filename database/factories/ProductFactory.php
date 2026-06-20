<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'        => $this->faker->words(3, true),
            'category'    => $this->faker->randomElement(['Semen', 'Besi & Baja', 'Cat', 'Keramik']),
            'price'       => $this->faker->numberBetween(5000, 500000),
            'unit'        => $this->faker->randomElement(['sak', 'batang', 'biji', 'm²']),
            'image'       => 'https://images.unsplash.com/photo-1523293915678-d126868e96f1?w=800',
            'description' => $this->faker->sentence(10),
            'stock'       => $this->faker->numberBetween(10, 500),
        ];
    }
}
