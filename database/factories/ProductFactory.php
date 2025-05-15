<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


class ProductFactory extends Factory
{

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph,
            'price' => $this->faker->randomFloat(2, 10, 500),
            'in_stock' => $this->faker->boolean(80),
            'category' => $this->faker->randomElement(['electronics', 'fashion', 'books', 'fitness']),
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'tags' => $this->faker->words(3),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
