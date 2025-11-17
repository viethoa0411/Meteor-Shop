<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class ProductVariantSeeder extends Seeder
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
            $variants = [
                [
                    'color_name' => $product->color,
                    'color_code' => $product->color_code,
                    'length' => $product->length,
                    'width' => $product->width,
                    'height' => $product->height,
                    'price' => $product->price,
                    'stock' => max(5, (int) round($product->stock / 2)),
                    'sku' => 'PV-' . str_pad($product->id, 3, '0', STR_PAD_LEFT) . '-A',
                ],
                [
                    'color_name' => trim(($product->color ?? '') . ' Signature'),
                    'color_code' => sprintf('#%06X', ($product->id * 12345) % 0xFFFFFF),
                    'length' => $product->length,
                    'width' => $product->width,
                    'height' => $product->height,
                    'price' => $product->price * 1.1,
                    'stock' => max(3, (int) round($product->stock / 3)),
                    'sku' => 'PV-' . str_pad($product->id, 3, '0', STR_PAD_LEFT) . '-B',
                ],
            ];

            foreach ($variants as $variant) {
                ProductVariant::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'sku' => $variant['sku'],
                    ],
                    array_merge($variant, ['product_id' => $product->id])
                );
            }
        }
    }
}
