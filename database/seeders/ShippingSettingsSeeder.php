<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShippingSetting;

class ShippingSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Cài đặt giá phí vận chuyển mẫu để test
     */
    public function run(): void
    {
        $settings = ShippingSetting::first();
        
        if (!$settings) {
            $settings = new ShippingSetting();
        }

        // Cài đặt địa chỉ gốc (kho hàng)
        $settings->origin_city = 'Hà Nội';
        $settings->origin_district = 'Nam Từ Liêm';
        $settings->origin_ward = 'Phương Canh';
        $settings->origin_address = 'Số 123 Phương Canh';

        // Cài đặt phí theo kích thước (đơn vị: VNĐ/mét)
        // Chiều dài
        $settings->first_length_price = 8000;   // Mét đầu tiên: 8,000đ
        $settings->next_length_price = 5000;    // Mỗi mét tiếp theo: 5,000đ

        // Chiều rộng
        $settings->first_width_price = 6000;    // Mét đầu tiên: 6,000đ
        $settings->next_width_price = 4000;     // Mỗi mét tiếp theo: 4,000đ

        // Chiều cao
        $settings->first_height_price = 6000;   // Mét đầu tiên: 6,000đ
        $settings->next_height_price = 4000;    // Mỗi mét tiếp theo: 4,000đ

        // Cân nặng (đơn vị: VNĐ/kg)
        $settings->first_weight_price = 10000;  // Kg đầu tiên: 10,000đ
        $settings->next_weight_price = 3000;    // Mỗi kg tiếp theo: 3,000đ

        // Phí cơ bản và phí theo km (không dùng nữa, nhưng giữ lại cho tương thích)
        $settings->base_fee = 0;
        $settings->fee_per_km = 0;

        // Ngưỡng miễn phí vận chuyển
        $settings->free_shipping_threshold = 10000000;  // 10 triệu
        $settings->free_shipping_enabled = true;

        // Phí theo khu vực (không dùng nữa, nhưng giữ lại)
        $settings->inner_city_fee = 30000;
        $settings->outer_city_fee = 50000;
        $settings->other_province_fee = 80000;

        // Phụ phí giao nhanh/hỏa tốc
        $settings->express_surcharge_type = 'percent';
        $settings->express_surcharge_value = 20;  // 20%
        $settings->express_label = 'Giao hàng nhanh (1-2 ngày)';

        $settings->fast_surcharge_type = 'percent';
        $settings->fast_surcharge_value = 40;     // 40%
        $settings->fast_label = 'Giao hàng hỏa tốc (trong ngày)';

        // Phí lắp đặt
        $settings->installation_fee = 200000;     // 200,000đ

        $settings->save();

        $this->command->info("✅ Đã cập nhật cài đặt phí vận chuyển:");
        $this->command->info("   - Chiều dài: Mét đầu 8,000đ, mét tiếp theo 5,000đ");
        $this->command->info("   - Chiều rộng: Mét đầu 6,000đ, mét tiếp theo 4,000đ");
        $this->command->info("   - Chiều cao: Mét đầu 6,000đ, mét tiếp theo 4,000đ");
        $this->command->info("   - Cân nặng: Kg đầu 10,000đ, kg tiếp theo 3,000đ");
        $this->command->info("   - Miễn phí ship từ 10,000,000đ");
    }
}

