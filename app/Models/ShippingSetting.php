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
    ];

    protected $casts = [
        'base_fee' => 'decimal:0',
        'fee_per_km' => 'decimal:0',
        'free_shipping_threshold' => 'decimal:0',
        'inner_city_fee' => 'decimal:0',
        'outer_city_fee' => 'decimal:0',
        'other_province_fee' => 'decimal:0',
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
            ]);
        }
        
        return $settings;
    }

    /**
     * Tính phí vận chuyển dựa trên địa chỉ
     */
    public function calculateShippingFee($customerCity, $customerDistrict, $subtotal)
    {
        // Miễn phí nếu đơn >= ngưỡng miễn phí (10 triệu)
        if ($subtotal >= $this->free_shipping_threshold) {
            return 0;
        }

        // Tính phí dựa trên khu vực
        $originCity = $this->normalizeCity($this->origin_city);
        $destCity = $this->normalizeCity($customerCity);

        // Cùng tỉnh/thành phố
        if ($originCity === $destCity) {
            $originDistrict = $this->normalizeDistrict($this->origin_district);
            $destDistrict = $this->normalizeDistrict($customerDistrict);
            
            // Cùng quận/huyện (nội thành)
            if ($originDistrict === $destDistrict) {
                return $this->inner_city_fee;
            }
            
            // Khác quận/huyện (ngoại thành)
            return $this->outer_city_fee;
        }

        // Khác tỉnh
        return $this->other_province_fee;
    }

    /**
     * Chuẩn hóa tên thành phố để so sánh
     */
    private function normalizeCity($city)
    {
        if (empty($city)) return '';
        
        $city = mb_strtolower(trim($city), 'UTF-8');
        // Loại bỏ tiền tố "Thành phố", "Tỉnh"
        $city = preg_replace('/^(thành phố|tỉnh|tp\.?)\s*/iu', '', $city);
        return trim($city);
    }

    /**
     * Chuẩn hóa tên quận/huyện để so sánh
     */
    private function normalizeDistrict($district)
    {
        if (empty($district)) return '';
        
        $district = mb_strtolower(trim($district), 'UTF-8');
        // Loại bỏ tiền tố "Quận", "Huyện", "Thị xã"
        $district = preg_replace('/^(quận|huyện|thị xã|tx\.?)\s*/iu', '', $district);
        return trim($district);
    }
}

