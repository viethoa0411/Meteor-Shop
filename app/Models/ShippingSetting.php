<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingSetting extends Model
{
    protected $fillable = [
        'origin_address',
        'origin_city',
        'origin_district',
        'origin_ward',
        'base_fee',
        'fee_per_km',
        'free_shipping_threshold',
        'inner_city_fee',
        'outer_city_fee',
        'other_province_fee',
        'first_length_price',
        'next_length_price',
        'first_width_price',
        'next_width_price',
        'first_height_price',
        'next_height_price',
        'first_weight_price',
        'next_weight_price',
        'express_surcharge_type',
        'express_surcharge_value',
        'fast_surcharge_type',
        'fast_surcharge_value',
        'express_label',
        'fast_label',
    ];

    protected $casts = [
        'base_fee' => 'decimal:0',
        'fee_per_km' => 'decimal:0',
        'free_shipping_threshold' => 'decimal:0',
        'inner_city_fee' => 'decimal:0',
        'outer_city_fee' => 'decimal:0',
        'other_province_fee' => 'decimal:0',
        'first_length_price' => 'decimal:0',
        'next_length_price' => 'decimal:0',
        'first_width_price' => 'decimal:0',
        'next_width_price' => 'decimal:0',
        'first_height_price' => 'decimal:0',
        'next_height_price' => 'decimal:0',
        'first_weight_price' => 'decimal:0',
        'next_weight_price' => 'decimal:0',
        'express_surcharge_value' => 'decimal:0',
        'fast_surcharge_value' => 'decimal:0',
    ];

    /**
     * Lấy cài đặt vận chuyển (singleton pattern)
     */
    public static function getSettings()
    {
        $settings = self::first();
        
        if (!$settings) {
            // Tạo cài đặt mặc định nếu chưa có
            $settings = self::create([
                'origin_city' => 'Hà Nội',
                'origin_district' => 'Cầu Giấy',
                'origin_ward' => 'Dịch Vọng',
                'origin_address' => '123 Đường ABC',
                'base_fee' => 30000,
                'fee_per_km' => 5000,
                'free_shipping_threshold' => 10000000, // 10 triệu
                'inner_city_fee' => 30000,
                'outer_city_fee' => 50000,
                'other_province_fee' => 80000,
                'first_length_price' => 10000,
                'next_length_price' => 5000,
                'first_width_price' => 8000,
                'next_width_price' => 4000,
                'first_height_price' => 8000,
                'next_height_price' => 4000,
                'first_weight_price' => 15000,
                'next_weight_price' => 7000,
                'express_surcharge_type' => 'percent',
                'express_surcharge_value' => 20,
                'fast_surcharge_type' => 'percent',
                'fast_surcharge_value' => 40,
                'express_label' => 'Giao hàng nhanh',
                'fast_label' => 'Giao hàng hỏa tốc',
            ]);
        }
        
        return $settings;
    }

    /**
     * Tính phí vận chuyển cho danh sách hàng hóa dựa trên kích thước, cân nặng và phương thức.
     *
     * @param array  $items  Danh sách item dạng:
     *  [
     *      [
     *          'length_cm' => 120,
     *          'width_cm'  => 60,
     *          'height_cm' => 40,
     *          'weight_kg' => 5,
     *          'quantity'  => 2,
     *      ],
     *  ]
     * @param string $method    standard|express|fast
     * @param float  $subtotal  Tổng giá trị đơn để áp dụng miễn phí ship (nếu có)
     *
     * @return array ['total' => float, 'standard_fee' => float, 'surcharge' => float]
     */
    public function calculateShippingFee(array $items, string $method = 'standard', float $subtotal = 0): array
    {
        $standardFee = 0;

        foreach ($items as $item) {
            $standardFee += $this->calculateStandardFeeForItem(
                (float)($item['length_cm'] ?? 0),
                (float)($item['width_cm'] ?? 0),
                (float)($item['height_cm'] ?? 0),
                (float)($item['weight_kg'] ?? 0),
                (int)($item['quantity'] ?? 1)
            );
        }

        // Miễn phí ship nếu đạt ngưỡng
        if ($this->free_shipping_threshold > 0 && $subtotal >= $this->free_shipping_threshold) {
            return [
                'standard_fee' => 0,
                'surcharge' => 0,
                'total' => 0,
            ];
        }

        $surcharge = $this->calculateSurcharge($method, $standardFee);

        return [
            'standard_fee' => $standardFee,
            'surcharge' => $surcharge,
            'total' => max(0, $standardFee + $surcharge),
        ];
    }

    /**
     * Tính phí tiêu chuẩn cho 1 item (nhân với quantity).
     */
    public function calculateStandardFeeForItem(
        float $lengthCm,
        float $widthCm,
        float $heightCm,
        float $weightKg,
        int $quantity = 1
    ): float {
        $lengthMeters = max(0, $lengthCm / 100);
        $widthMeters = max(0, $widthCm / 100);
        $heightMeters = max(0, $heightCm / 100);
        $weightKg = max(0, $weightKg);

        $fee = $this->calculateDimensionFee($lengthMeters, $this->first_length_price, $this->next_length_price);
        $fee += $this->calculateDimensionFee($widthMeters, $this->first_width_price, $this->next_width_price);
        $fee += $this->calculateDimensionFee($heightMeters, $this->first_height_price, $this->next_height_price);

        if ($weightKg > 0) {
            $extraWeightUnit = max(0, ceil($weightKg - 1));
            $fee += $this->first_weight_price + ($extraWeightUnit * $this->next_weight_price);
        }

        return max(0, $fee * max(1, $quantity));
    }

    /**
     * Tính phụ phí cho phương thức nhanh/hỏa tốc dựa trên phí tiêu chuẩn.
     */
    public function calculateSurcharge(string $method, float $standardFee): float
    {
        if ($standardFee <= 0 || $method === 'standard') {
            return 0;
        }

        $type = $method === 'fast' ? $this->fast_surcharge_type : $this->express_surcharge_type;
        $value = $method === 'fast' ? $this->fast_surcharge_value : $this->express_surcharge_value;

        if ($type === 'percent') {
            return round(($standardFee * $value) / 100);
        }

        return max(0, $value);
    }

    /**
     * Helper tính phí theo chiều dài/chiều rộng/chiều cao.
     */
    private function calculateDimensionFee(float $meters, float $firstPrice, float $nextPrice): float
    {
        if ($meters <= 0) {
            return 0;
        }

        $extraUnit = max(0, ceil($meters - 1));
        return $firstPrice + ($extraUnit * $nextPrice);
    }
}

