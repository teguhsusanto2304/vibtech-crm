<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Nama model yang sesuai dengan factory ini.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Definisikan state default model.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Gunakan factory lain untuk membuat data relasi
            'product_category_id' => ProductCategory::factory(),
            'created_by' => User::factory(),
            
            // Definisikan data dummy untuk kolom lain
            'name' => $this->faker->unique()->words(2, true),
            'sku_no' => $this->faker->unique()->ean8,
            'quantity' => $this->faker->numberBetween(0, 100),
            'image' => null,
        ];
    }
}
