<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrderDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = Order::all();
        $products = Product::all();

        if ($orders->isEmpty()) {
            $this->call(OrderSeeder::class);
            $orders = Order::all();
        }

        if ($products->isEmpty()) {
            $this->call(ProductSeeder::class);
            $products = Product::all();
        }

        foreach ($orders as $order) {
            $selectedProducts = $products->random(min(3, $products->count()));
            $total = 0;

            foreach ($selectedProducts as $index => $product) {
                $quantity = $index + 1;
                $price = $product->price;
                $subtotal = $price * $quantity;

                OrderDetail::updateOrCreate(
                    [
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                    ],
                    [
                        'product_name' => $product->name,
                        'image_path' => $product->image,
                        'quantity' => $quantity,
                        'price' => $price,
                        'subtotal' => $subtotal,
                    ]
                );

                $total += $subtotal;
            }

            $order->update([
                'total_price' => $total,
                'final_total' => max(0, $total - $order->discount_amount),
            ]);
        }
    }
}
