<?php

namespace App\Services;

use App\Models\Promotion;
use App\Models\UserPromotionUsage;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class PromotionService
{
    public function validateAndCalculate($user, string $code, array $items, float $orderSubtotal): array
    {
        $promotion = Promotion::where('code', $code)->first();
        if (!$promotion) {
            return ['ok' => false, 'error' => 'Mã khuyến mãi không tồn tại'];
        }

        if ($promotion->status !== 'active') {
            return ['ok' => false, 'error' => 'Mã khuyến mãi không hoạt động'];
        }

        $now = now();
        if ($promotion->start_date && $promotion->start_date->gt($now)) {
            return ['ok' => false, 'error' => 'Mã khuyến mãi chưa đến thời gian áp dụng'];
        }
        if ($promotion->end_date && $promotion->end_date->lt($now)) {
            return ['ok' => false, 'error' => 'Mã khuyến mãi đã hết hạn'];
        }

        if (!is_null($promotion->limit_global) && $promotion->used_count >= $promotion->limit_global) {
            return ['ok' => false, 'error' => 'Mã khuyến mãi đã đạt giới hạn toàn hệ thống'];
        }
        if (!is_null($promotion->usage_limit) && $promotion->used_count >= $promotion->usage_limit) {
            return ['ok' => false, 'error' => 'Mã khuyến mãi đã đạt giới hạn tổng'];
        }

        if ($user) {
            $usage = UserPromotionUsage::where('user_id', $user->id)->where('promotion_id', $promotion->id)->first();
            if (!is_null($promotion->limit_per_user) && $usage && $usage->used_count >= $promotion->limit_per_user) {
                return ['ok' => false, 'error' => 'Bạn đã dùng mã quá số lần cho phép'];
            }

            $completedOrders = Order::where('user_id', $user->id)
                ->whereIn('order_status', ['completed', 'delivered'])
                ->count();
            if ($promotion->min_orders > 0 && $completedOrders < $promotion->min_orders) {
                return ['ok' => false, 'error' => 'Bạn chưa đạt số lần mua tối thiểu'];
            }
        }

        if ($promotion->min_amount > 0 && $orderSubtotal < $promotion->min_amount) {
            return ['ok' => false, 'error' => 'Giá trị đơn hàng chưa đạt mức tối thiểu'];
        }

        $eligibleAmount = $this->calculateEligibleAmount($promotion, $items);
        if ($eligibleAmount <= 0) {
            return ['ok' => false, 'error' => 'Mã không áp dụng cho sản phẩm trong giỏ'];
        }

        $discount = 0.0;
        if ($promotion->discount_type === 'percent') {
            $discount = $eligibleAmount * ($promotion->discount_value / 100);
            if (!is_null($promotion->max_discount) && $discount > $promotion->max_discount) {
                $discount = (float) $promotion->max_discount;
            }
        } else {
            $discount = (float) $promotion->discount_value;
            if ($discount > $eligibleAmount) {
                $discount = $eligibleAmount;
            }
        }

        return [
            'ok' => true,
            'discount' => round($discount, 2),
            'promotion_id' => $promotion->id,
            'code' => $promotion->code,
        ];
    }

    private function calculateEligibleAmount(Promotion $promotion, array $items): float
    {
        if ($promotion->scope === 'all') {
            return array_reduce($items, fn($c, $i) => $c + ($i['subtotal'] ?? 0), 0.0);
        }

        if ($promotion->scope === 'category') {
            $allowed = $promotion->categories()->pluck('categories.id')->toArray();
            $sum = 0.0;
            foreach ($items as $i) {
                $catId = $i['product']->category_id ?? null;
                if ($catId && in_array($catId, $allowed)) {
                    $sum += $i['subtotal'] ?? 0;
                }
            }
            return $sum;
        }

        if ($promotion->scope === 'product') {
            $allowed = $promotion->products()->pluck('products.id')->toArray();
            $sum = 0.0;
            foreach ($items as $i) {
                $pid = $i['product_id'] ?? ($i['product']->id ?? null);
                if ($pid && in_array($pid, $allowed)) {
                    $sum += $i['subtotal'] ?? 0;
                }
            }
            return $sum;
        }

        return 0.0;
    }

    public function incrementUsage($userId, $promotionId): void
    {
        DB::transaction(function () use ($userId, $promotionId) {
            $usage = UserPromotionUsage::firstOrCreate([
                'user_id' => $userId,
                'promotion_id' => $promotionId,
            ], ['used_count' => 0]);
            $usage->increment('used_count');

            Promotion::where('id', $promotionId)->update([
                'used_count' => DB::raw('used_count + 1')
            ]);
        });
    }
}

