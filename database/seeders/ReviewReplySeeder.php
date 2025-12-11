<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\ReviewReply;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewReplySeeder extends Seeder
{
    /**
     * Tạo thêm admin replies cho reviews (nếu cần bổ sung)
     */
    public function run(): void
    {
        $reviews = Review::where('status', 'approved')
            ->whereDoesntHave('replies')
            ->get();

        $admins = User::where('role', 'admin')->get();

        if ($reviews->isEmpty() || $admins->isEmpty()) {
            return;
        }

        // Tạo replies cho 30% reviews chưa có reply
        $replyCount = (int)($reviews->count() * 0.3);
        $reviewsToReply = $reviews->random(min($replyCount, $reviews->count()));

        $replies = [
            // Positive replies
            'Cảm ơn bạn đã đánh giá sản phẩm. Chúng tôi rất vui khi bạn hài lòng!',
            'Cảm ơn bạn đã tin tưởng và ủng hộ shop. Chúc bạn có trải nghiệm tốt!',
            'Rất vui khi nhận được phản hồi tích cực từ bạn. Chúng tôi sẽ tiếp tục cố gắng!',
            
            // Neutral replies
            'Cảm ơn bạn đã phản hồi. Chúng tôi sẽ cải thiện chất lượng sản phẩm.',
            'Cảm ơn bạn đã mua sản phẩm. Nếu có vấn đề gì, vui lòng liên hệ bộ phận CSKH.',
            
            // Negative replies
            'Xin lỗi vì trải nghiệm không tốt. Vui lòng liên hệ với chúng tôi để được hỗ trợ.',
            'Chúng tôi rất tiếc về trải nghiệm của bạn. Sẽ cố gắng cải thiện chất lượng.',
            'Cảm ơn bạn đã phản hồi. Chúng tôi sẽ xem xét và cải thiện sản phẩm.',
        ];

        foreach ($reviewsToReply as $review) {
            $admin = $admins->random();
            
            // Chọn reply phù hợp với rating
            if ($review->rating >= 4) {
                $content = $replies[array_rand(array_slice($replies, 0, 3))];
            } elseif ($review->rating == 3) {
                $content = $replies[array_rand(array_slice($replies, 3, 2))];
            } else {
                $content = $replies[array_rand(array_slice($replies, 5))];
            }

            ReviewReply::create([
                'review_id' => $review->id,
                'admin_id' => $admin->id,
                'content' => $content,
                'created_at' => $review->created_at->addDays(rand(1, 5)),
            ]);
        }

        $this->command->info('Đã tạo thêm ' . count($reviewsToReply) . ' admin replies cho reviews.');
    }
}


