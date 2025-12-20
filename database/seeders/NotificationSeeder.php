<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin users
        $admins = User::where('role', 'admin')->get();
        
        if ($admins->isEmpty()) {
            $this->command->warn('No admin users found. Please create admin users first.');
            return;
        }

        foreach ($admins as $admin) {
            // Create sample notifications for each admin
            $notifications = [
                // Order notifications
                [
                    'user_id' => $admin->id,
                    'type' => 'order',
                    'level' => 'info',
                    'title' => 'Đơn hàng mới',
                    'message' => 'Đơn hàng #1234 cần xử lý',
                    'url' => '/admin/orders/1234',
                    'icon' => 'bi-cart-check-fill',
                    'icon_color' => 'text-info',
                    'is_read' => false,
                    'created_at' => Carbon::now()->subMinutes(5),
                ],
                [
                    'user_id' => $admin->id,
                    'type' => 'order',
                    'level' => 'danger',
                    'title' => 'Thanh toán thất bại',
                    'message' => 'Đơn hàng #1235 thanh toán thất bại',
                    'url' => '/admin/orders/1235',
                    'icon' => 'bi-cart-check-fill',
                    'icon_color' => 'text-danger',
                    'is_read' => false,
                    'created_at' => Carbon::now()->subMinutes(15),
                ],
                [
                    'user_id' => $admin->id,
                    'type' => 'order',
                    'level' => 'success',
                    'title' => 'Giao hàng thành công',
                    'message' => 'Đơn hàng #1230 đã giao thành công',
                    'url' => '/admin/orders/1230',
                    'icon' => 'bi-cart-check-fill',
                    'icon_color' => 'text-success',
                    'is_read' => true,
                    'read_at' => Carbon::now()->subMinutes(30),
                    'created_at' => Carbon::now()->subHours(2),
                ],
                
                // Product notifications
                [
                    'user_id' => $admin->id,
                    'type' => 'product',
                    'level' => 'warning',
                    'title' => 'Sản phẩm sắp hết hàng',
                    'message' => 'Ghế sofa hiện đại chỉ còn 5 sản phẩm',
                    'url' => '/admin/products/1/edit',
                    'icon' => 'bi-box-seam-fill',
                    'icon_color' => 'text-warning',
                    'is_read' => false,
                    'created_at' => Carbon::now()->subMinutes(30),
                ],
                [
                    'user_id' => $admin->id,
                    'type' => 'product',
                    'level' => 'danger',
                    'title' => 'Sản phẩm hết hàng',
                    'message' => 'Bàn ăn gỗ tự nhiên đã hết hàng',
                    'url' => '/admin/products/2/edit',
                    'icon' => 'bi-box-seam-fill',
                    'icon_color' => 'text-danger',
                    'is_read' => false,
                    'created_at' => Carbon::now()->subHours(1),
                ],
                
                // Review notifications
                [
                    'user_id' => $admin->id,
                    'type' => 'review',
                    'level' => 'info',
                    'title' => 'Bình luận chờ duyệt',
                    'message' => 'Bình luận mới cho sản phẩm: Tủ quần áo 4 cánh',
                    'url' => '/admin/comments?status=pending',
                    'icon' => 'bi-chat-left-text-fill',
                    'icon_color' => 'text-info',
                    'is_read' => false,
                    'created_at' => Carbon::now()->subMinutes(10),
                ],
                [
                    'user_id' => $admin->id,
                    'type' => 'review',
                    'level' => 'warning',
                    'title' => 'Review tiêu cực',
                    'message' => 'Sản phẩm Giường ngủ có review 2 sao',
                    'url' => '/admin/comments/10',
                    'icon' => 'bi-chat-left-text-fill',
                    'icon_color' => 'text-warning',
                    'is_read' => true,
                    'read_at' => Carbon::now()->subMinutes(20),
                    'created_at' => Carbon::now()->subHours(1),
                ],
                
                // Contact notifications
                [
                    'user_id' => $admin->id,
                    'type' => 'contact',
                    'level' => 'warning',
                    'title' => 'Liên hệ mới',
                    'message' => 'Nguyễn Văn A đã gửi liên hệ',
                    'url' => '/admin/contacts',
                    'icon' => 'bi-envelope-fill',
                    'icon_color' => 'text-warning',
                    'is_read' => false,
                    'created_at' => Carbon::now()->subMinutes(25),
                ],
                
                // User notifications
                [
                    'user_id' => $admin->id,
                    'type' => 'user',
                    'level' => 'info',
                    'title' => 'Khách hàng mới',
                    'message' => 'Trần Thị B vừa đăng ký tài khoản',
                    'url' => '/admin/users',
                    'icon' => 'bi-person-fill',
                    'icon_color' => 'text-info',
                    'is_read' => false,
                    'created_at' => Carbon::now()->subHours(2),
                ],
                
                // Chat notifications
                [
                    'user_id' => $admin->id,
                    'type' => 'chat',
                    'level' => 'info',
                    'title' => 'Tin nhắn mới',
                    'message' => 'Lê Văn C đã gửi tin nhắn',
                    'url' => '/admin/chat',
                    'icon' => 'bi-chat-dots-fill',
                    'icon_color' => 'text-info',
                    'is_read' => false,
                    'created_at' => Carbon::now()->subMinutes(40),
                ],
                
                // Voucher notifications
                [
                    'user_id' => $admin->id,
                    'type' => 'voucher',
                    'level' => 'warning',
                    'title' => 'Voucher sắp hết hạn',
                    'message' => 'Voucher GIAM20 sẽ hết hạn trong 3 ngày',
                    'url' => '/admin/vouchers',
                    'icon' => 'bi-ticket-perforated-fill',
                    'icon_color' => 'text-warning',
                    'is_read' => false,
                    'created_at' => Carbon::now()->subDays(1),
                ],
                
                // Shipping notifications
                [
                    'user_id' => $admin->id,
                    'type' => 'shipping',
                    'level' => 'success',
                    'title' => 'Tạo vận đơn thành công',
                    'message' => 'Vận đơn #VD001 đã được tạo',
                    'url' => '/admin/shipping',
                    'icon' => 'bi-truck-fill',
                    'icon_color' => 'text-success',
                    'is_read' => true,
                    'read_at' => Carbon::now()->subHours(3),
                    'created_at' => Carbon::now()->subHours(4),
                ],
                
                // Security notifications
                [
                    'user_id' => $admin->id,
                    'type' => 'security',
                    'level' => 'info',
                    'title' => 'Đăng nhập admin',
                    'message' => $admin->name . ' vừa đăng nhập',
                    'url' => '/admin/users/' . $admin->id,
                    'icon' => 'bi-shield-fill',
                    'icon_color' => 'text-info',
                    'is_read' => true,
                    'read_at' => Carbon::now()->subMinutes(60),
                    'created_at' => Carbon::now()->subMinutes(60),
                ],
            ];

            foreach ($notifications as $notification) {
                Notification::create($notification);
            }
        }

        $this->command->info('Sample notifications created successfully!');
    }
}

