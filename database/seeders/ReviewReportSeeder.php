<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\ReviewReport;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewReportSeeder extends Seeder
{
    /**
     * Tạo thêm reports cho reviews (nếu cần bổ sung)
     */
    public function run(): void
    {
        $reviews = Review::where('reported_count', 0)
            ->where('status', '!=', 'hidden')
            ->get();
        
        $users = User::where('role', 'user')->get();

        if ($reviews->isEmpty() || $users->isEmpty()) {
            return;
        }

        // Tạo thêm một số reports ngẫu nhiên
        $reportCount = min(10, $reviews->count());
        $reviewsToReport = $reviews->random($reportCount);

        $reasons = ['spam', 'offensive', 'false_info', 'inappropriate', 'other'];
        $descriptions = [
            'Nội dung spam',
            'Ngôn từ xúc phạm',
            'Thông tin sai sự thật',
            'Nội dung không phù hợp',
            'Quảng cáo không liên quan',
        ];

        foreach ($reviewsToReport as $review) {
            $reportUser = $users->where('id', '!=', $review->user_id)->random();
            $reason = $reasons[array_rand($reasons)];

            ReviewReport::create([
                'review_id' => $review->id,
                'user_id' => $reportUser->id,
                'reason' => $reason,
                'description' => rand(1, 100) <= 60 ? $descriptions[array_rand($descriptions)] : null,
                'created_at' => now()->subDays(rand(1, 30)),
            ]);

            // Cập nhật reported_count
            $review->increment('reported_count');
        }

        $this->command->info('Đã tạo thêm ' . $reportCount . ' reports cho reviews.');
    }
}


