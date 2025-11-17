<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;

class ProductImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();

        if ($products->isEmpty()) {
            $this->call(ProductSeeder::class);
            $products = Product::all();
        }

        foreach ($products as $product) {
            $gallery = [
                'products/gallery/' . $product->slug . '-1.jpg',
                'products/gallery/' . $product->slug . '-2.jpg',
            ];

            foreach ($gallery as $path) {
                ProductImage::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'image' => $path,
                    ],
                    [
                        'product_id' => $product->id,
                        'image' => $path,
                    ]
                );
            }
        }
    }
}
