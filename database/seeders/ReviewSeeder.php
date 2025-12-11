<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\ReviewReport;
use App\Models\ReviewReply;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy sản phẩm và users
        $products = Product::where('status', 'active')->get();
        $users = User::where('role', 'user')->get();

        if ($products->isEmpty() || $users->isEmpty()) {
            $this->command->warn('Không có sản phẩm hoặc users để tạo reviews. Vui lòng chạy ProductSeeder và UserSeeder trước.');
            return;
        }

        $reviews = [];
        $statuses = ['pending', 'approved', 'approved', 'approved', 'rejected', 'hidden']; // More approved
        $ratings = [1, 2, 3, 4, 5, 4, 5, 5, 4, 3]; // Mix of ratings, more positive

        // Comments mẫu
        $positiveComments = [
            'Sản phẩm rất tốt, chất lượng cao, đóng gói cẩn thận. Tôi rất hài lòng!',
            'Giao hàng nhanh, sản phẩm đúng như mô tả. Sẽ mua lại lần sau.',
            'Rất đẹp và chất lượng tốt. Đáng giá tiền bạc.',
            'Sản phẩm tuyệt vời, tôi rất thích. Khuyến nghị mọi người nên mua.',
            'Chất lượng tốt, giá cả hợp lý. Đã mua nhiều lần và rất hài lòng.',
            'Sản phẩm đẹp, đúng như hình ảnh. Giao hàng nhanh chóng.',
            'Rất hài lòng với sản phẩm này. Chất lượng vượt mong đợi.',
            'Tuyệt vời! Sản phẩm đẹp và bền. Sẽ ủng hộ shop tiếp.',
        ];

        $neutralComments = [
            'Sản phẩm ổn, không có gì đặc biệt. Giá cả hợp lý.',
            'Chất lượng tạm được, đúng với giá tiền.',
            'Sản phẩm bình thường, không có vấn đề gì.',
            'OK, đúng như mô tả. Giao hàng hơi chậm một chút.',
        ];

        $negativeComments = [
            'Chất lượng không như mong đợi. Hơi thất vọng.',
            'Sản phẩm có vấn đề nhỏ, nhưng vẫn dùng được.',
            'Không đúng như mô tả. Hơi thất vọng.',
        ];

        // Tạo reviews cho mỗi sản phẩm
        foreach ($products as $product) {
            // Tạo 5-10 reviews cho mỗi sản phẩm
            $reviewCount = rand(5, 10);
            
            for ($i = 0; $i < $reviewCount; $i++) {
                $user = $users->random();
                $rating = $ratings[array_rand($ratings)];
                $status = $statuses[array_rand($statuses)];
                
                // Chọn comment phù hợp với rating
                if ($rating >= 4) {
                    $comment = $positiveComments[array_rand($positiveComments)];
                } elseif ($rating == 3) {
                    $comment = $neutralComments[array_rand($neutralComments)];
                } else {
                    $comment = $negativeComments[array_rand($negativeComments)];
                }

                // 30% có ảnh
                $hasImages = rand(1, 100) <= 30;
                $images = null;
                if ($hasImages) {
                    $imageCount = rand(1, 3);
                    $images = [];
                    for ($j = 0; $j < $imageCount; $j++) {
                        // Placeholder images - bạn có thể thay bằng ảnh thật
                        $images[] = 'reviews/sample-review-' . rand(1, 5) . '.jpg';
                    }
                }

                // 70% là verified purchase
                $isVerified = rand(1, 100) <= 70;

                // 10% bị report
                $reportedCount = rand(1, 100) <= 10 ? rand(1, 3) : 0;

                $review = Review::create([
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                    'rating' => $rating,
                    'content' => $comment,
                    'images' => $images,
                    'is_verified_purchase' => $isVerified,
                    'status' => $status,
                    'reported_count' => $reportedCount,
                    'created_at' => now()->subDays(rand(1, 90)), // Random date trong 90 ngày qua
                ]);

                $reviews[] = $review;

                // Tạo reports nếu có
                if ($reportedCount > 0) {
                    $reportReasons = ['spam', 'offensive', 'false_info', 'inappropriate', 'other'];
                    $availableUsers = $users->where('id', '!=', $user->id);
                    
                    if ($availableUsers->count() > 0) {
                        $actualReportCount = min($reportedCount, $availableUsers->count());
                        $reportUsers = $availableUsers->random($actualReportCount);
                        
                        if (!($reportUsers instanceof \Illuminate\Support\Collection)) {
                            $reportUsers = collect([$reportUsers]);
                        }

                        foreach ($reportUsers as $reportUser) {
                            ReviewReport::create([
                                'review_id' => $review->id,
                                'user_id' => $reportUser->id,
                                'reason' => $reportReasons[array_rand($reportReasons)],
                                'description' => rand(1, 100) <= 50 ? 'Nội dung không phù hợp' : null,
                                'created_at' => $review->created_at->addDays(rand(1, 7)),
                            ]);
                        }
                    }
                }

                // 20% có admin reply (chỉ với approved reviews)
                if ($status === 'approved' && rand(1, 100) <= 20) {
                    $admin = User::where('role', 'admin')->first();
                    if ($admin) {
                        $adminReplies = [
                            'Cảm ơn bạn đã đánh giá sản phẩm. Chúng tôi rất vui khi bạn hài lòng!',
                            'Cảm ơn bạn đã phản hồi. Chúng tôi sẽ cải thiện chất lượng sản phẩm.',
                            'Xin lỗi vì trải nghiệm không tốt. Vui lòng liên hệ với chúng tôi để được hỗ trợ.',
                            'Cảm ơn bạn đã mua sản phẩm. Nếu có vấn đề gì, vui lòng liên hệ bộ phận CSKH.',
                            'Chúng tôi rất trân trọng phản hồi của bạn. Sẽ cố gắng phục vụ tốt hơn.',
                        ];

                        ReviewReply::create([
                            'review_id' => $review->id,
                            'admin_id' => $admin->id,
                            'content' => $adminReplies[array_rand($adminReplies)],
                            'created_at' => $review->created_at->addDays(rand(1, 3)),
                        ]);
                    }
                }
            }
        }

        // Cập nhật rating trung bình cho các sản phẩm
        foreach ($products as $product) {
            $approvedReviews = Review::where('product_id', $product->id)
                ->where('status', 'approved')
                ->get();

            if ($approvedReviews->count() > 0) {
                $product->rating_avg = round($approvedReviews->avg('rating'), 2);
                $product->save();
            }
        }

        $this->command->info('Đã tạo ' . count($reviews) . ' reviews thành công!');
        $this->command->info('- Reviews approved: ' . Review::approved()->count());
        $this->command->info('- Reviews pending: ' . Review::pending()->count());
        $this->command->info('- Reviews reported: ' . Review::reported()->count());
        $this->command->info('- Reviews with images: ' . Review::withImages()->count());
    }
}
