<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy user có email user2@example.com
        $user = User::where('email', 'user2@example.com')->first();

        if (!$user) {
            $this->command->warn('Không tìm thấy user với email user2@example.com. Vui lòng chạy UserSeeder trước.');
            return;
        }

        // Lấy tất cả sản phẩm active để tạo đơn hàng
        $products = Product::where('status', 'active')->get();

        if ($products->isEmpty()) {
            $this->command->warn('Không có sản phẩm nào. Vui lòng chạy ProductSeeder trước.');
            return;
        }

        // Tạo 2-3 đơn hàng đã hoàn thành
        $numberOfOrders = rand(2, 3);
        
        for ($i = 1; $i <= $numberOfOrders; $i++) {
            // Tạo đơn hàng đã hoàn thành
            $orderDate = now()->subDays(rand(5, 30));
            $deliveredDate = $orderDate->copy()->addDays(rand(2, 5));
            
            $order = Order::create([
                'user_id' => $user->id,
                'promotion_id' => null,
                'order_code' => 'ORD-' . strtoupper(Str::random(8)) . '-' . time() . '-' . $i,
                'total_price' => 0, // Sẽ tính sau
                'discount_amount' => 0,
                'final_total' => 0, // Sẽ tính sau
                'sub_total' => 0, // Sẽ tính sau
                'payment_method' => collect(['cash', 'bank', 'momo'])->random(),
                'payment_status' => 'paid',
                'order_status' => 'completed', // Đã hoàn thành
                'shipping_address' => $user->address ?? '456 Đường XYZ, Quận 2, TP.HCM',
                'shipping_phone' => $user->phone ?? '0923456789',
                'shipping_method' => 'standard',
                'shipping_fee' => 30000,
                'customer_name' => $user->name,
                'customer_phone' => $user->phone ?? '0923456789',
                'customer_email' => $user->email,
                'shipping_city' => 'TP.HCM',
                'shipping_district' => 'Quận 2',
                'shipping_ward' => 'Phường An Phú',
                'order_date' => $orderDate,
                'confirmed_at' => $orderDate->copy()->addHours(rand(1, 6)),
                'packed_at' => $orderDate->copy()->addDays(1),
                'shipped_at' => $orderDate->copy()->addDays(rand(1, 2)),
                'delivered_at' => $deliveredDate, // Đã giao hàng - đảm bảo order đã hoàn thành
            ]);

            // Tạo order details
            $subTotal = 0;
            $orderDetails = [];

            // Chọn ngẫu nhiên 2-4 sản phẩm
            $productCount = min(rand(2, 4), $products->count());
            $selectedProducts = $products->shuffle()->take($productCount);

            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 2); // Giảm số lượng để tránh giá trị quá lớn
                $price = $product->sale_price ?? $product->price;
                // Đảm bảo giá không quá lớn (max 10 triệu)
                if ($price > 10000000) {
                    $price = 10000000;
                }
                $subtotal = $price * $quantity;

                $orderDetail = OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'variant_id' => null,
                    'variant_name' => null,
                    'variant_sku' => null,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $subtotal,
                    'total_price' => $subtotal,
                    'image_path' => $product->image,
                ]);

                $orderDetails[] = $orderDetail;
                $subTotal += $subtotal;
            }

            // Cập nhật tổng tiền cho đơn hàng
            $shippingFee = 30000;
            $discountAmount = 0;
            $finalTotal = $subTotal + $shippingFee - $discountAmount;

            $order->update([
                'sub_total' => $subTotal,
                'total_price' => $subTotal,
                'discount_amount' => $discountAmount,
                'final_total' => $finalTotal,
            ]);

            $this->command->info("✓ Đã tạo đơn hàng #{$i}: {$order->order_code}");
            $this->command->info("  - Số sản phẩm: {$productCount}");
            $this->command->info("  - Tổng tiền: " . number_format($finalTotal, 0, ',', '.') . " VNĐ");
            $this->command->info("  - Trạng thái: {$order->order_status}");
            $this->command->info("  - Ngày giao: {$deliveredDate->format('d/m/Y H:i')}");
            $this->command->info("");
        }

        $this->command->info("✅ Đã tạo thành công {$numberOfOrders} đơn hàng đã hoàn thành cho user {$user->email}!");
    }
}

