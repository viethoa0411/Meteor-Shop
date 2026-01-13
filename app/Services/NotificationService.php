<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Create notification for admin users
     */
    public static function createForAdmins(array $data): void
    {
        try {
            $admins = User::where('role', 'admin')->get();
            
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => $data['type'],
                    'level' => $data['level'] ?? 'info',
                    'title' => $data['title'],
                    'message' => $data['message'],
                    'url' => $data['url'] ?? null,
                    'icon' => $data['icon'] ?? self::getDefaultIcon($data['type']),
                    'icon_color' => $data['icon_color'] ?? self::getDefaultIconColor($data['level'] ?? 'info'),
                    'metadata' => $data['metadata'] ?? null,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error creating notifications for admins:', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
        }
    }

    /**
     * Create notification for specific user
     */
    public static function createForUser(int $userId, array $data): Notification
    {
        try {
            return Notification::create([
                'user_id' => $userId,
                'type' => $data['type'],
                'level' => $data['level'] ?? 'info',
                'title' => $data['title'],
                'message' => $data['message'],
                'url' => $data['url'] ?? null,
                'icon' => $data['icon'] ?? self::getDefaultIcon($data['type']),
                'icon_color' => $data['icon_color'] ?? self::getDefaultIconColor($data['level'] ?? 'info'),
                'metadata' => $data['metadata'] ?? null,
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating notification for user:', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Create notification for current user
     */
    public static function createForCurrentUser(array $data): Notification
    {
        if (!Auth::check()) {
            throw new \Exception('User not authenticated');
        }

        return self::createForUser(Auth::id(), $data);
    }

    /**
     * Create order notification
     */
    public static function notifyNewOrder($order): void
    {
        self::createForAdmins([
            'type' => 'order',
            'level' => 'info',
            'title' => 'Đơn hàng mới',
            'message' => 'Đơn hàng #' . $order->id . ' cần xử lý',
            'url' => route('admin.orders.show', $order->id),
            'metadata' => ['order_id' => $order->id]
        ]);
    }

    /**
     * Notify user about return request status update
     */
    public static function notifyReturnStatusUpdate($order, $status): void
    {
        $statusText = match($status) {
            'approved' => 'được chấp nhận',
            'rejected' => 'bị từ chối',
            'completed' => 'hoàn tất',
            'refunded' => 'đã hoàn tiền',
            'received' => 'đã nhận được hàng hoàn',
            default => 'được cập nhật',
        };

        $level = match($status) {
            'approved', 'completed', 'refunded', 'received' => 'success',
            'rejected' => 'danger',
            default => 'info',
        };
        
        $title = 'Cập nhật yêu cầu trả hàng';
        $orderCode = $order->order_code ?? $order->id;
        $message = "Yêu cầu trả hàng cho đơn hàng #{$orderCode} đã {$statusText}.";

        self::createForUser($order->user_id, [
            'type' => 'order',
            'level' => $level,
            'title' => $title,
            'message' => $message,
            'url' => route('client.account.orders.show', $order->id),
            'metadata' => ['order_id' => $order->id, 'return_status' => $status]
        ]);
    }

    /**
     * Create review notification
     */
    public static function notifyNewReview($review): void
    {
        $productName = $review->product ? $review->product->name : 'N/A';
        
        self::createForAdmins([
            'type' => 'review',
            'level' => 'info',
            'title' => 'Bình luận chờ duyệt',
            'message' => 'Bình luận mới cho sản phẩm: ' . \Illuminate\Support\Str::limit($productName, 50),
            'url' => route('admin.comments.index', ['status' => 'pending']),
            'metadata' => ['review_id' => $review->id]
        ]);
    }

    /**
     * Create contact notification
     */
    public static function notifyNewContact($contact): void
    {
        self::createForAdmins([
            'type' => 'contact',
            'level' => 'warning',
            'title' => 'Liên hệ mới',
            'message' => $contact->name . ' đã gửi liên hệ',
            'url' => route('admin.contacts.index'),
            'metadata' => ['contact_id' => $contact->id]
        ]);
    }

    /**
     * Create low stock notification
     */
    public static function notifyLowStock($product): void
    {
        self::createForAdmins([
            'type' => 'product',
            'level' => 'warning',
            'title' => 'Sản phẩm sắp hết hàng',
            'message' => $product->name . ' chỉ còn ' . $product->stock . ' sản phẩm',
            'url' => route('admin.products.edit', $product->id),
            'metadata' => ['product_id' => $product->id, 'stock' => $product->stock]
        ]);
    }

    /**
     * Create payment failed notification
     */
    public static function notifyPaymentFailed($order): void
    {
        self::createForAdmins([
            'type' => 'order',
            'level' => 'danger',
            'title' => 'Thanh toán thất bại',
            'message' => 'Đơn hàng #' . $order->id . ' thanh toán thất bại',
            'url' => route('admin.orders.show', $order->id),
            'metadata' => ['order_id' => $order->id]
        ]);
    }

    /**
     * Create return request notification
     */
    public static function notifyReturnRequest($order): void
    {
        self::createForAdmins([
            'type' => 'order',
            'level' => 'warning',
            'title' => 'Yêu cầu trả hàng mới',
            'message' => 'Đơn hàng #' . ($order->order_code ?? $order->id) . ' có yêu cầu trả hàng',
            'url' => route('admin.orders.returns.show', $order->id),
            'metadata' => ['order_id' => $order->id]
        ]);
    }

    /**
     * Get default icon for notification type
     */
    private static function getDefaultIcon(string $type): string
    {
        return match($type) {
            'order' => 'bi-cart-check-fill',
            'product' => 'bi-box-seam-fill',
            'review' => 'bi-chat-left-text-fill',
            'chat' => 'bi-chat-dots-fill',
            'contact' => 'bi-envelope-fill',
            'deposit' => 'bi-wallet2',
            'withdraw' => 'bi-cash-coin',
            'user' => 'bi-person-fill',
            'voucher' => 'bi-ticket-perforated-fill',
            'shipping' => 'bi-truck-fill',
            'security' => 'bi-shield-fill',
            default => 'bi-bell-fill',
        };
    }

    /**
     * Get default icon color for level
     */
    private static function getDefaultIconColor(string $level): string
    {
        return match($level) {
            'info' => 'text-info',
            'warning' => 'text-warning',
            'danger' => 'text-danger',
            'success' => 'text-success',
            default => 'text-primary',
        };
    }
}

