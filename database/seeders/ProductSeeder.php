<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Database\Seeder;
use App\Models\ProductAttribute;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::pluck('id'); // Get all user IDs

        Product::factory()
            ->count(10)
            ->create()
            ->each(function ($product) use ($users) {
                // Add attributes
                ProductAttribute::factory()->count(3)->create([
                    'product_id' => $product->id,
                ]);

                // Add reviews from random users
                ProductReview::factory()->count(5)->make()->each(function ($review) use ($product, $users) {
                    $review->product_id = $product->id;
                    $review->user_id = $users->random();
                    $review->save();
                });
            });
    }
}
