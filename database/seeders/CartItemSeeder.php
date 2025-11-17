<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CartItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $carts = DB::table('carts')->get();
        $products = Product::all();

        if ($carts->isEmpty()) {
            $this->call(CartSeeder::class);
            $carts = DB::table('carts')->get();
        }

        if ($products->isEmpty()) {
            $this->call(ProductSeeder::class);
            $products = Product::all();
        }

        foreach ($carts as $cart) {
            $selectedProducts = $products->random(min(2, $products->count()));
            $cartTotal = 0;

            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 3);
                $price = $product->price;
                $subtotal = $price * $quantity;

                DB::table('cart_items')->updateOrInsert(
                    [
                        'cart_id' => $cart->id,
                        'product_id' => $product->id,
                    ],
                    [
                        'quantity' => $quantity,
                        'price' => $price,
                        'subtotal' => $subtotal,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                $cartTotal += $subtotal;
            }

            DB::table('carts')->where('id', $cart->id)->update([
                'total_price' => $cartTotal,
                'updated_at' => now(),
            ]);
        }
    }
}
