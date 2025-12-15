<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Carbon\Carbon;

class CreateSampleNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:create-sample {--user-id= : Specific user ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create sample notifications for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user-id');
        
        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                $this->error("User with ID {$userId} not found!");
                return 1;
            }
            $users = collect([$user]);
        } else {
            // Get all admin users
            $users = User::where('role', 'admin')->get();
            
            if ($users->isEmpty()) {
                $this->error('No admin users found!');
                return 1;
            }
        }

        $this->info('Creating sample notifications...');

        foreach ($users as $user) {
            // Using NotificationService
            try {
                // Sample order notification
                NotificationService::createForUser($user->id, [
                    'type' => 'order',
                    'level' => 'info',
                    'title' => 'Đơn hàng mới (Sample)',
                    'message' => 'Đơn hàng #' . rand(1000, 9999) . ' cần xử lý',
                    'url' => '/admin/orders',
                ]);

                // Sample product notification
                NotificationService::createForUser($user->id, [
                    'type' => 'product',
                    'level' => 'warning',
                    'title' => 'Sản phẩm sắp hết hàng (Sample)',
                    'message' => 'Sản phẩm mẫu chỉ còn ' . rand(1, 10) . ' sản phẩm',
                    'url' => '/admin/products',
                ]);

                // Sample review notification
                NotificationService::createForUser($user->id, [
                    'type' => 'review',
                    'level' => 'info',
                    'title' => 'Bình luận chờ duyệt (Sample)',
                    'message' => 'Bình luận mới cho sản phẩm: Sản phẩm mẫu',
                    'url' => '/admin/comments',
                ]);

                // Sample contact notification
                NotificationService::createForUser($user->id, [
                    'type' => 'contact',
                    'level' => 'warning',
                    'title' => 'Liên hệ mới (Sample)',
                    'message' => 'Khách hàng mẫu đã gửi liên hệ',
                    'url' => '/admin/contacts',
                ]);

                $this->info("Created sample notifications for user: {$user->name} (ID: {$user->id})");
            } catch (\Exception $e) {
                $this->error("Error creating notifications for user {$user->id}: " . $e->getMessage());
            }
        }

        $this->info('Sample notifications created successfully!');
        return 0;
    }
}

