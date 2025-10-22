<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Tạo admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@meteor-shop.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '0123456789',
            'address' => '123 Admin Street, Ho Chi Minh City',
            'status' => 'active',
        ]);

        // Tạo staff user
        User::create([
            'name' => 'Staff',
            'email' => 'staff@meteor-shop.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'phone' => '0987654321',
            'address' => '456 Staff Street, Ho Chi Minh City',
            'status' => 'active',
        ]);

        // Tạo khách hàng
        $customers = User::factory(10)->create([
            'role' => 'user',
            'status' => 'active',
        ]);

        // Tạo danh mục
        $categories = [
            ['name' => 'Điện thoại', 'slug' => 'dien-thoai', 'description' => 'Điện thoại thông minh'],
            ['name' => 'Laptop', 'slug' => 'laptop', 'description' => 'Máy tính xách tay'],
            ['name' => 'Phụ kiện', 'slug' => 'phu-kien', 'description' => 'Phụ kiện điện tử'],
            ['name' => 'Đồng hồ', 'slug' => 'dong-ho', 'description' => 'Đồng hồ thông minh'],
        ];

        foreach ($categories as $categoryData) {
            Category::create($categoryData);
        }

        // Tạo thương hiệu
        $brands = [
            ['name' => 'Apple', 'slug' => 'apple', 'description' => 'Apple Inc.'],
            ['name' => 'Samsung', 'slug' => 'samsung', 'description' => 'Samsung Electronics'],
            ['name' => 'Xiaomi', 'slug' => 'xiaomi', 'description' => 'Xiaomi Corporation'],
            ['name' => 'Dell', 'slug' => 'dell', 'description' => 'Dell Technologies'],
            ['name' => 'HP', 'slug' => 'hp', 'description' => 'Hewlett Packard'],
        ];

        foreach ($brands as $brandData) {
            Brand::create($brandData);
        }

        // Tạo sản phẩm
        $products = [
            [
                'name' => 'iPhone 15 Pro Max',
                'slug' => 'iphone-15-pro-max',
                'description' => 'iPhone 15 Pro Max với chip A17 Pro mạnh mẽ',
                'price' => 29990000,
                'stock' => 50,
                'category_id' => 1,
                'brand_id' => 1,
                'status' => 'active',
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'slug' => 'samsung-galaxy-s24-ultra',
                'description' => 'Samsung Galaxy S24 Ultra với camera 200MP',
                'price' => 24990000,
                'stock' => 30,
                'category_id' => 1,
                'brand_id' => 2,
                'status' => 'active',
            ],
            [
                'name' => 'MacBook Pro M3',
                'slug' => 'macbook-pro-m3',
                'description' => 'MacBook Pro với chip M3 mạnh mẽ',
                'price' => 45990000,
                'stock' => 20,
                'category_id' => 2,
                'brand_id' => 1,
                'status' => 'active',
            ],
            [
                'name' => 'Dell XPS 13',
                'slug' => 'dell-xps-13',
                'description' => 'Dell XPS 13 với thiết kế cao cấp',
                'price' => 32990000,
                'stock' => 25,
                'category_id' => 2,
                'brand_id' => 4,
                'status' => 'active',
            ],
            [
                'name' => 'AirPods Pro 2',
                'slug' => 'airpods-pro-2',
                'description' => 'AirPods Pro thế hệ 2 với chống ồn',
                'price' => 5990000,
                'stock' => 100,
                'category_id' => 3,
                'brand_id' => 1,
                'status' => 'active',
            ],
            [
                'name' => 'Apple Watch Series 9',
                'slug' => 'apple-watch-series-9',
                'description' => 'Apple Watch Series 9 với nhiều tính năng mới',
                'price' => 8990000,
                'stock' => 40,
                'category_id' => 4,
                'brand_id' => 1,
                'status' => 'active',
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }

        // Tạo mã khuyến mãi
        $promotions = [
            [
                'code' => 'WELCOME10',
                'name' => 'Chào mừng khách hàng mới',
                'description' => 'Giảm 10% cho đơn hàng đầu tiên',
                'discount_type' => 'percent',
                'discount_value' => 10,
                'start_date' => now(),
                'end_date' => now()->addDays(30),
                'usage_limit' => 100,
                'used_count' => 0,
                'status' => 'active',
            ],
            [
                'code' => 'SAVE50K',
                'name' => 'Tiết kiệm 50K',
                'description' => 'Giảm 50,000 VNĐ cho đơn hàng từ 1 triệu',
                'discount_type' => 'fixed',
                'discount_value' => 50000,
                'start_date' => now(),
                'end_date' => now()->addDays(15),
                'usage_limit' => 50,
                'used_count' => 0,
                'status' => 'active',
            ],
        ];

        foreach ($promotions as $promotionData) {
            Promotion::create($promotionData);
        }

        // Tạo đơn hàng mẫu
        $orderStatuses = ['pending', 'processing', 'completed', 'cancelled'];
        $paymentStatuses = ['pending', 'paid', 'failed'];
        $paymentMethods = ['cash', 'bank', 'momo', 'paypal'];

        for ($i = 0; $i < 20; $i++) {
            $customer = $customers->random();
            $orderStatus = $orderStatuses[array_rand($orderStatuses)];
            $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];
            $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
            
            // Tạo mã đơn hàng
            $orderCode = 'ORD-' . strtoupper(Str::random(8));
            
            // Chọn sản phẩm ngẫu nhiên (1-3 sản phẩm)
            $selectedProducts = Product::inRandomOrder()->limit(rand(1, 3))->get();
            
            $totalPrice = 0;
            $orderDetails = [];
            
            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 3);
                $price = $product->price;
                $subtotal = $price * $quantity;
                
                $totalPrice += $subtotal;
                
                $orderDetails[] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $subtotal,
                ];
            }
            
            // Tính giảm giá (30% cơ hội có mã khuyến mãi)
            $discountAmount = 0;
            $promotionId = null;
            if (rand(1, 100) <= 30) {
                $promotion = Promotion::inRandomOrder()->first();
                if ($promotion && $promotion->status === 'active') {
                    $promotionId = $promotion->id;
                    if ($promotion->discount_type === 'percent') {
                        $discountAmount = ($totalPrice * $promotion->discount_value) / 100;
                    } else {
                        $discountAmount = $promotion->discount_value;
                    }
                }
            }
            
            $shippingFee = rand(0, 50000);
            $finalTotal = $totalPrice - $discountAmount + $shippingFee;
            
            // Tạo đơn hàng
            $order = Order::create([
                'user_id' => $customer->id,
                'promotion_id' => $promotionId,
                'order_code' => $orderCode,
                'total_price' => $totalPrice,
                'discount_amount' => $discountAmount,
                'final_total' => $finalTotal,
                'shipping_fee' => $shippingFee,
                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,
                'order_status' => $orderStatus,
                'shipping_address' => $customer->address ?? '123 Main Street, Ho Chi Minh City',
                'shipping_phone' => $customer->phone ?? '0123456789',
                'notes' => rand(1, 100) <= 20 ? 'Giao hàng nhanh' : null,
                'created_at' => now()->subDays(rand(0, 30)),
            ]);
            
            // Tạo chi tiết đơn hàng
            foreach ($orderDetails as $detail) {
                $order->orderDetails()->create($detail);
            }
            
            // Cập nhật số lần sử dụng promotion
            if ($promotionId) {
                Promotion::where('id', $promotionId)->increment('used_count');
            }
        }

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin: admin@meteor-shop.com / password');
        $this->command->info('Staff: staff@meteor-shop.com / password');
    }
}