<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShippingSetting;

class UpdateShippingPriceSeeder extends Seeder
{
    /**
     * Cập nhật giá phí vận chuyển theo ví dụ
     */
    public function run(): void
    {
        $settings = ShippingSetting::first();
        
        if (!$settings) {
            $settings = new ShippingSetting();
        }

        // Cập nhật giá phí theo ví dụ
        $settings->first_length_price = 5000;   // Mét đầu tiên: 5,000đ
        $settings->next_length_price = 2000;    // Mỗi mét tiếp theo: 2,000đ

        $settings->first_width_price = 4000;    // Mét đầu tiên: 4,000đ
        $settings->next_width_price = 1500;     // Mỗi mét tiếp theo: 1,500đ

        $settings->first_height_price = 6000;   // Mét đầu tiên: 6,000đ
        $settings->next_height_price = 2500;    // Mỗi mét tiếp theo: 2,500đ

        $settings->first_weight_price = 3000;   // Kg đầu tiên: 3,000đ
        $settings->next_weight_price = 1000;    // Mỗi kg tiếp theo: 1,000đ

        $settings->save();

        $this->command->info("✅ Đã cập nhật giá phí vận chuyển:");
        $this->command->info("   📏 Chiều dài: Mét đầu 5,000đ | Mét tiếp theo 2,000đ");
        $this->command->info("   📐 Chiều rộng: Mét đầu 4,000đ | Mét tiếp theo 1,500đ");
        $this->command->info("   📊 Chiều cao: Mét đầu 6,000đ | Mét tiếp theo 2,500đ");
        $this->command->info("   ⚖️  Cân nặng: Kg đầu 3,000đ | Kg tiếp theo 1,000đ");
    }
}

