<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\OrderLog;

class AutoCompleteDeliveredOrders extends Command
{
    protected $signature = 'orders:auto-complete-delivered';
    protected $description = 'Tự động chuyển đơn hàng từ Đã giao sang Hoàn thành sau 2 ngày nếu khách chưa xác nhận';

    public function handle(): int
    {
        $threshold = now()->subDays(2);

        $orders = Order::where('order_status', 'delivered')
            ->whereNotNull('delivered_at')
            ->where('delivered_at', '<=', $threshold)
            ->get();

        foreach ($orders as $order) {
            $oldStatus = $order->order_status;
            $order->update([
                'order_status' => 'completed',
                'updated_at' => now(),
            ]);

            if (Schema::hasTable('order_status_history')) {
                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'admin_id' => null,
                    'old_status' => $oldStatus,
                    'new_status' => 'completed',
                    'note' => 'Auto-complete sau 2 ngày kể từ khi giao hàng',
                ]);
            }

            if (Schema::hasTable('order_logs')) {
                OrderLog::create([
                    'order_id' => $order->id,
                    'status' => 'completed',
                    'updated_by' => null,
                    'role' => 'system',
                    'created_at' => now(),
                ]);
            }
        }

        $this->info('Đã xử lý ' . $orders->count() . ' đơn hàng delivered → completed.');
        return Command::SUCCESS;
    }
}

