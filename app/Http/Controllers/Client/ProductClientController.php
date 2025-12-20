<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;

use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

use App\Models\Review;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


class ProductClientController extends Controller
{
    public function productsByCategory($slug)
    {
        Category::where('slug', $slug)->firstOrFail();

        return redirect()->route('client.product.search', ['category' => $slug]);
    }
    public function showDetail($slug)
    {
        $cate = Category::all();

        // Lấy sản phẩm với đầy đủ relationships
        $product = Product::with([
            'images',
            'variants',
            'category',
            'reviews.user',
        ])
        ->where('slug', $slug)
        ->where('status', 'active')
        ->firstOrFail();

        // Lấy sản phẩm + ảnh phụ
        $product = Product::with('images')->where('slug', $slug)->firstOrFail();

        // Lấy sản phẩm liên quan
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->take(8)
            ->get();

        // Kiểm tra đã ở trong wishlist chưa
        $isInWishlist = false;
        if (Auth::check()) {
            $isInWishlist = Wishlist::where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->exists();
        }

        // ✅ Tính rating trung bình và breakdown từ reviews đã approved
        $approvedReviews = $product->reviews()
            ->where('status', 'approved')
            ->get();

        $ratingAvg = $approvedReviews->avg('rating') ?? 0;
        $totalReviews = $approvedReviews->count();

        $ratingBreakdown = [];
        for ($i = 5; $i >= 1; $i--) {
            $ratingBreakdown[$i] = $approvedReviews->where('rating', $i)->count();
        }

        // ✅ Nhớ truyền thêm các biến rating vào view
        return view('client.products.detail', compact(
            'product',
            'relatedProducts',
            'cate',
            'isInWishlist',
            'ratingAvg',
            'totalReviews',
            'ratingBreakdown'
        ));
    }


    /**
     * API endpoint để lấy thông tin variant động (giá, stock)
     */
    public function getVariant(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'color' => 'nullable|string',
            'size' => 'nullable|string',
        ]);

        $product = Product::with('variants')->findOrFail($request->product_id);

        $variant = null;
        if ($request->color && $request->size) {
            // Parse size (format: "lengthxwidthxheight")
            $sizeParts = explode('x', $request->size);
            $length = isset($sizeParts[0]) ? (int)$sizeParts[0] : null;
            $width = isset($sizeParts[1]) ? (int)$sizeParts[1] : null;
            $height = isset($sizeParts[2]) ? (int)$sizeParts[2] : null;

            $variant = $product->variants()
                ->where('color_name', $request->color)
                ->where('length', $length)
                ->where('width', $width)
                ->where('height', $height)
                ->first();
        }

        if ($variant) {
            return response()->json([
                'status' => 'success',
                'variant' => [
                    'id' => $variant->id,
                    'price' => $variant->price ?? $product->price,
                    'stock' => $variant->stock,
                    'sku' => $variant->sku,
                ]
            ]);
        }

        return response()->json([
            'status' => 'success',
            'variant' => [
                'price' => $product->price,
                'stock' => $product->stock ?? 0,
                'sku' => $product->sku ?? '',
            ]
        ]);
    }

    /**
     * API endpoint để lấy danh sách reviews với filter và pagination
     */
    public function getReviews(Request $request, $slug)
    {
        try {
            $product = Product::where('slug', $slug)->firstOrFail();

            [$filters, $pagination] = $this->extractReviewFilters($request);

            // Query reviews đã approved + áp dụng filter & sort
            $query = $this->buildReviewsQuery($product->id, $filters);

            $reviews = $query->paginate(
                $pagination['per_page'],
                ['*'],
                'page',
                $pagination['page']
            );

            // Tính thống kê rating & tổng quan
            [$ratingStats, $allApprovedReviews] = $this->buildRatingStats($product->id);

            // Format dữ liệu review cho frontend
            $currentUser = $request->user();
            $formattedReviews = $reviews
                ->map(fn($review) => $this->formatReviewForClient($review, $currentUser))
                ->filter(fn($review) => isset($review['id']) && $review['id'] > 0);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'average_rating' => round($ratingStats['average'], 1),
                    'total_reviews' => $ratingStats['total'],
                    'reviews_with_images' => $ratingStats['with_images'],
                    'verified_reviews' => $ratingStats['verified'],
                    'rating_breakdown' => $ratingStats['breakdown'],
                    'reviews' => $formattedReviews,
                    'pagination' => [
                        'current_page' => $reviews->currentPage(),
                        'last_page' => $reviews->lastPage(),
                        'per_page' => $reviews->perPage(),
                        'total' => $reviews->total(),
                    ],
                ],
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sản phẩm không tồn tại',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error in getReviews', [
                'slug' => $slug,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Có lỗi xảy ra khi tải đánh giá. Vui lòng thử lại sau.',
            ], 500);
        }
    }

    /**
     * Tách các filter & tham số phân trang từ request.
     */
    protected function extractReviewFilters(Request $request): array
    {
        $filters = [
            'rating' => $request->input('rating'),
            'has_images' => $request->input('has_images'),
            'verified' => $request->input('verified'),
            'sort' => $request->input('sort', 'newest'), // newest, helpful
        ];

        $pagination = [
            'page' => max(1, (int) $request->input('page', 1)),
            'per_page' => max(1, min(50, (int) $request->input('per_page', 10))),
        ];

        return [$filters, $pagination];
    }

    /**
     * Xây dựng query reviews đã approved với các filter & sort.
     */
    protected function buildReviewsQuery(int $productId, array $filters)
    {
        $query = Review::where('product_id', $productId)
                ->where('status', 'approved')
                ->with(['user', 'replies.admin', 'helpfulVotes']);

        // Filter theo rating
        if (!empty($filters['rating']) && $filters['rating'] !== 'all') {
            $query->where('rating', $filters['rating']);
        }

        // Filter có hình ảnh
        if ($filters['has_images'] === '1' || $filters['has_images'] === true) {
            $query->whereNotNull('images')
                  ->where('images', '!=', '[]');
        }

        // Filter verified buyer
        if ($filters['verified'] === '1' || $filters['verified'] === true) {
            $query->where('is_verified_purchase', true);
        }

        // Sort
        if ($filters['sort'] === 'helpful') {
            // Sắp xếp theo số lượt hữu ích giảm dần
            $query->withCount('helpfulVotes')
                  ->orderBy('helpful_votes_count', 'desc')
                  ->orderBy('created_at', 'desc'); // Nếu bằng nhau thì sắp xếp theo mới nhất
        } else {
            $query->latest();
        }

        return $query;
    }

    /**
     * Tính toán rating breakdown & thống kê tổng quan cho sản phẩm.
     *
     * @return array{0: array, 1: \Illuminate\Support\Collection}
     */
    protected function buildRatingStats(int $productId): array
    {
        $allApprovedReviews = Review::where('product_id', $productId)
            ->where('status', 'approved')
            ->get();

        $ratingBreakdown = [];
        for ($i = 5; $i >= 1; $i--) {
            $ratingBreakdown[$i] = $allApprovedReviews->where('rating', $i)->count();
        }

        $averageRating = $allApprovedReviews->avg('rating') ?? 0;
        $totalReviews = $allApprovedReviews->count();
        $reviewsWithImages = $allApprovedReviews->filter(function ($review) {
            return !empty($review->images) && is_array($review->images) && count($review->images) > 0;
        })->count();
        $verifiedReviews = $allApprovedReviews->where('is_verified_purchase', true)->count();

        return [
            [
                'average' => $averageRating,
                'total' => $totalReviews,
                'with_images' => $reviewsWithImages,
                'verified' => $verifiedReviews,
                'breakdown' => $ratingBreakdown,
            ],
            $allApprovedReviews,
        ];
    }

    /**
     * Chuẩn hoá dữ liệu review trả về cho client (ẩn tên, xử lý ảnh, replies, helpful, ...).
     */
    protected function formatReviewForClient(Review $review, $currentUser): array
    {
        try {
            $userName = $review->user->name ?? 'Người dùng';

            // Ẩn tên: Nguyễn V*** (giữ 1 ký tự đầu + ***)
            $nameParts = explode(' ', $userName);
            if (count($nameParts) > 1) {
                $firstName = $nameParts[0];
                $lastName = end($nameParts);
                $hiddenName = substr($firstName, 0, 1) . '*** ' . substr($lastName, 0, 1) . '***';
            } else {
                $hiddenName = substr($userName, 0, 1) . '***';
            }

            $imageUrls = $this->formatReviewImages($review);

                $adminReplies = [];
                if ($review->replies && $review->replies->isNotEmpty()) {
                    $adminReplies = $review->replies->map(function ($reply) {
                        return [
                            'content' => $reply->content ?? '',
                            'admin_name' => $reply->admin->name ?? 'Admin',
                            'created_at' => $reply->created_at ? $reply->created_at->format('d/m/Y') : '',
                        ];
                    })->values()->toArray();
                }
                
                return [
                    'id' => $review->id ?? 0,
                    'user_name' => $hiddenName ?? 'Người dùng',
                    'user_initial' => strtoupper(substr($userName, 0, 1)) ?: 'U',
                    'rating' => $review->rating ?? 0,
                    'content' => $review->content ?? $review->comment ?? '',
                    'images' => $imageUrls,
                    'is_verified_buyer' => $review->is_verified_purchase ?? false,
                    'created_at' => $review->created_at ? $review->created_at->format('d/m/Y') : '',
                    'created_at_full' => $review->created_at ? $review->created_at->format('d/m/Y H:i') : '',
                    'created_at_human' => $review->created_at ? $review->created_at->diffForHumans() : '',
                    'helpful_count' => $review->helpfulVotes ? $review->helpfulVotes->count() : 0,
                'is_helpful' => ($currentUser && $review->helpfulVotes)
                    ? $review->helpfulVotes->pluck('user_id')->contains($currentUser->id)
                    : false,
                    'admin_replies' => $adminReplies,
                    'admin_reply' => $adminReplies[0] ?? null,
                ];
            } catch (\Exception $e) {
                Log::error('Error formatting review', [
                    'review_id' => $review->id ?? 'unknown',
                    'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                ]);

                // Return minimal data để không break frontend
                return [
                    'id' => $review->id ?? 0,
                    'user_name' => 'Người dùng',
                    'user_initial' => 'U',
                    'rating' => 0,
                    'content' => '',
                    'images' => [],
                    'is_verified_buyer' => false,
                    'created_at' => '',
                    'created_at_full' => '',
                    'created_at_human' => '',
                    'helpful_count' => 0,
                    'is_helpful' => false,
                    'admin_reply' => null,
                ];
            }
    }

    /**
     * Chuẩn hoá danh sách ảnh của một review, trả về mảng URL tuyệt đối.
     */
    protected function formatReviewImages(Review $review): array
    {
        $images = $review->images;
        $imageUrls = [];

        if (empty($images)) {
            return [];
        }

        // Xử lý nếu images là JSON string
        if (is_string($images)) {
            $imagesArray = json_decode($images, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($imagesArray)) {
                $images = $imagesArray;
            } else {
                $images = [];
            }
        }

        // Xử lý nếu images là array
        if (is_array($images) && count($images) > 0) {
            foreach ($images as $img) {
                if (empty($img)) {
                    continue;
                }

                // Đảm bảo $img là string và normalize path
                $img = (string) $img;
                $img = str_replace('\\', '/', $img);
                $img = trim($img, '/');

                // Kiểm tra xem path đã có 'storage/' chưa
                if (strpos($img, 'storage/') === 0 || strpos($img, '/storage/') === 0) {
                    $img = ltrim($img, '/');
                    $imageUrls[] = asset($img);
                } elseif (strpos($img, 'http') === 0 || strpos($img, 'https') === 0) {
                    // Nếu là URL đầy đủ
                    $imageUrls[] = $img;
                } else {
                    // Path tương đối từ storage/app/public (ví dụ: reviews/image.jpg)
                    $cleanPath = ltrim(str_replace('\\', '/', $img), '/');

                    $storagePath = storage_path('app/public/' . $cleanPath);
                    $publicPath = public_path('storage/' . $cleanPath);
                    $existsInStorage = file_exists($storagePath);
                    $existsInPublic = file_exists($publicPath);

                    // Nếu file tồn tại trong storage nhưng không có trong public, copy nó
                    if ($existsInStorage && !$existsInPublic) {
                        $publicDir = dirname($publicPath);
                        if (!is_dir($publicDir)) {
                            mkdir($publicDir, 0755, true);
                        }
                        copy($storagePath, $publicPath);
                        $existsInPublic = file_exists($publicPath);
                    }

                    if ($existsInStorage || $existsInPublic) {
                        $imageUrls[] = asset('storage/' . $cleanPath);
                    } else {
                        Log::warning('Review image not found', [
                            'review_id' => $review->id,
                            'image_path' => $img,
                            'clean_path' => $cleanPath,
                            'storage_path' => $storagePath,
                            'public_path' => $publicPath,
                        ]);
                        $imageUrls[] = asset('storage/' . $cleanPath);
                    }
                }
            }
        }

        return $imageUrls;
    }

    /**
     * Store review for a product
     */
    public function storeReview(Request $request, $slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();

        // Check authentication - route has auth middleware, so user is guaranteed
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bạn cần đăng nhập để đánh giá.'
            ], 401);
        }

        // Check if user has purchased this product (only completed orders)
        $userHasPurchased = $user->orders()
            ->where('order_status', 'completed')
            ->whereHas('items', function ($q) use ($product) {
                $q->where('product_id', $product->id);
            })
            ->exists();

        if (!$userHasPurchased) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bạn cần mua sản phẩm này để có thể đánh giá.'
            ], 403);
        }

        // Get settings from config
        $allowImages = config('reviews.allow_images', true);
        $maxImages = config('reviews.max_images', 5);
        $autoApprove = config('reviews.auto_approve', false);
        $autoVerifyBuyer = config('reviews.auto_verify_buyer', true);
        $blacklistKeywords = config('reviews.blacklist_keywords', []);
        $autoHideBlacklist = config('reviews.auto_hide_blacklist', false);

        // Validate images based on settings
        $imageRules = [];
        if ($allowImages) {
            $imageRules = ['images.*' => 'nullable|image|max:2048'];
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:20|max:1000',
        ] + $imageRules, [
            'rating.required' => 'Vui lòng chọn đánh giá sao',
            'rating.integer' => 'Đánh giá sao không hợp lệ',
            'rating.min' => 'Đánh giá sao phải từ 1 đến 5',
            'rating.max' => 'Đánh giá sao phải từ 1 đến 5',
            'comment.required' => 'Vui lòng nhập nhận xét',
            'comment.min' => 'Nhận xét phải có tối thiểu 20 ký tự',
            'comment.max' => 'Nhận xét không được vượt quá 1000 ký tự',
            'images.*.image' => 'File phải là hình ảnh',
            'images.*.max' => 'Mỗi ảnh không được vượt quá 2MB',
        ]);

        // Check blacklist keywords
        $content = $request->input('comment', '');
        $containsBlacklist = false;
        if (!empty($blacklistKeywords) && !empty($content)) {
            foreach ($blacklistKeywords as $keyword) {
                if (stripos($content, $keyword) !== false) {
                    $containsBlacklist = true;
                    break;
                }
            }
        }

        // Determine status based on settings
        $status = 'pending';
        if ($autoApprove && !$containsBlacklist) {
            $status = 'approved';
        } elseif ($autoHideBlacklist && $containsBlacklist) {
            $status = 'hidden';
        }

        // Handle images
        $images = [];
        if ($allowImages && $request->hasFile('images')) {
            $uploadedImages = $request->file('images');
            $imageCount = min(count($uploadedImages), $maxImages);

            for ($i = 0; $i < $imageCount; $i++) {
                $path = $uploadedImages[$i]->store('reviews', 'public');
                // Normalize path: đảm bảo dùng forward slash
                $path = str_replace('\\', '/', $path);
                $images[] = $path;

                // Đảm bảo file cũng có trong public/storage (cho Windows symlink issues)
                $publicPath = public_path('storage/' . $path);
                $publicDir = dirname($publicPath);
                if (!is_dir($publicDir)) {
                    mkdir($publicDir, 0755, true);
                }
                $storagePath = storage_path('app/public/' . $path);
                if (file_exists($storagePath) && !file_exists($publicPath)) {
                    copy($storagePath, $publicPath);
                }

                // Log để debug
                Log::info('Review image uploaded', [
                    'original_path' => $path,
                    'normalized_path' => $path,
                    'storage_path' => $storagePath,
                    'public_path' => $publicPath,
                    'file_exists_storage' => file_exists($storagePath),
                    'file_exists_public' => file_exists($publicPath)
                ]);
            }
        }

        // Determine verified purchase
        $isVerifiedPurchase = $autoVerifyBuyer && $userHasPurchased;

        $review = Review::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'rating' => $request->rating,
            'content' => $content,
            'images' => $images,
            'is_verified_purchase' => $isVerifiedPurchase,
            'status' => $status,
        ]);

        // Tạo thông báo cho admin về review mới (chỉ khi status là pending)
        if ($status === 'pending') {
            try {
                \App\Services\NotificationService::notifyNewReview($review);
            } catch (\Exception $e) {
                // Không dừng flow nếu tạo notification thất bại
                Log::error('Error creating review notification: ' . $e->getMessage());
            }
        }
        
        // Tạo thông báo cho review tiêu cực (1-2 sao)
        if ($request->rating <= 2) {
            try {
                \App\Services\NotificationService::createForAdmins([
                    'type' => 'review',
                    'level' => 'warning',
                    'title' => 'Review tiêu cực',
                    'message' => 'Sản phẩm ' . $product->name . ' có review ' . $request->rating . ' sao',
                    'url' => route('admin.comments.index', ['review_id' => $review->id]) ?? route('admin.comments.index'),
                    'metadata' => ['review_id' => $review->id, 'rating' => $request->rating, 'product_id' => $product->id]
                ]);
            } catch (\Exception $e) {
                Log::error('Error creating negative review notification: ' . $e->getMessage());
            }
        }

        // Update product rating average (only if approved)
        if ($status === 'approved') {
            $this->updateProductRating($product->id);
        }

        $message = 'Đánh giá đã được gửi thành công!';
        if ($status === 'pending') {
            $message = 'Đánh giá của bạn đang chờ duyệt. Cảm ơn bạn đã phản hồi!';
        } elseif ($status === 'hidden') {
            $message = 'Đánh giá của bạn đã được gửi nhưng đang được kiểm duyệt.';
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'review_status' => $status
        ]);
    }

    /**
     * Mark review as helpful (toggle - like/unlike)
     */
    public function markHelpful(Request $request, $reviewId)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Bạn cần đăng nhập'], 401);
        }

        $review = Review::findOrFail($reviewId);

        // Check if user already marked this review as helpful
        $existingVote = \App\Models\ReviewHelpful::where('review_id', $reviewId)
            ->where('user_id', $user->id)
            ->first();

        if ($existingVote) {
            // User already liked, so unlike (remove vote)
            $existingVote->delete();
            $helpfulCount = $review->helpfulVotes()->count();

            return response()->json([
                'status' => 'success',
                'message' => 'Đã bỏ đánh dấu hữu ích',
                'helpful_count' => $helpfulCount,
                'is_helpful' => false
            ]);
        } else {
            // User hasn't liked yet, so like (add vote)
            \App\Models\ReviewHelpful::create([
                'review_id' => $reviewId,
                'user_id' => $user->id,
            ]);
            $helpfulCount = $review->helpfulVotes()->count();

            return response()->json([
                'status' => 'success',
                'message' => 'Cảm ơn bạn đã đánh dấu hữu ích!',
                'helpful_count' => $helpfulCount,
                'is_helpful' => true
            ]);
        }
    }

    /**
     * Report a review
     */
    public function reportReview(Request $request, $reviewId)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Bạn cần đăng nhập'], 401);
        }

        $request->validate([
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000'
        ]);

        $review = Review::findOrFail($reviewId);

        // Check if already reported by this user
        $existingReport = \App\Models\ReviewReport::where('review_id', $reviewId)
            ->where('user_id', $user->id)
            ->first();

        if ($existingReport) {
            return response()->json(['status' => 'error', 'message' => 'Bạn đã báo cáo đánh giá này rồi']);
        }

        // Create report
        \App\Models\ReviewReport::create([
            'review_id' => $reviewId,
            'user_id' => $user->id,
            'reason' => $request->reason,
            'description' => $request->description,
        ]);

        // Update reported_count
        $review->increment('reported_count');

        return response()->json([
            'status' => 'success',
            'message' => 'Cảm ơn bạn đã báo cáo. Chúng tôi sẽ xem xét đánh giá này.'
        ]);
    }

    /**
     * Check if reviews have been updated
     */
    public function checkUpdates(Request $request, $slug)
    {
        try {
            $product = Product::where('slug', $slug)->firstOrFail();
            $lastCheck = $request->input('last_check');

            if (!$lastCheck) {
                return response()->json([
                    'has_updates' => true,
                    'timestamp' => now()->toIso8601String()
                ]);
            }

            $lastCheckDate = Carbon::createFromTimestamp($lastCheck / 1000);

            $hasUpdates = Review::where('product_id', $product->id)
                ->where('status', 'approved')
                ->where(function ($q) use ($lastCheckDate) {
                    $q->where('created_at', '>', $lastCheckDate)
                        ->orWhere('updated_at', '>', $lastCheckDate);
                })
                ->exists();

            return response()->json([
                'has_updates' => $hasUpdates,
                'timestamp' => now()->toIso8601String()
            ]);
        } catch (\Exception $e) {
            Log::error('Error in checkUpdates', [
                'slug' => $slug,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'has_updates' => false,
                'timestamp' => now()->toIso8601String()
            ]);
        }
    }

    /**
     * Cập nhật rating trung bình của sản phẩm
     */
    protected function updateProductRating($productId)
    {
        $product = Product::find($productId);
        if (!$product) return;

        $approvedReviews = Review::where('product_id', $productId)
            ->where('status', 'approved')
            ->get();

        if ($approvedReviews->count() > 0) {
            $product->rating_avg = round($approvedReviews->avg('rating'), 2);
            $product->save();
        } else {
            $product->rating_avg = 0;
            $product->save();
        }
    }

    /**
     * Tìm kiếm sản phẩm với các filter
     *
     * @param Request $request
     * @param string|null $slug
     * @return \Illuminate\Contracts\View\View
            ->take(4)
            ->get();

        return view('client.products.detail', compact('product', 'relatedProducts', 'cate'));
    }

    /**
     * Lấy danh sách ID của danh mục và toàn bộ danh mục con (đệ quy/BFS).
     *
     * @param int $rootCategoryId ID của danh mục gốc
     * @return array Mảng chứa ID của danh mục gốc và tất cả danh mục con
     */
    public function search(Request $request, ?string $slug = null)
    {
        $searchQuery = trim($request->input('query'));
        $sort = $request->input('sort', 'newest');
        $categoryInput = $request->input('category');
        $minPrice = $request->input('minPrice');
        $maxPrice = $request->input('maxPrice');

        // Lấy danh mục đang hoạt động (cần thiết để truyền sang view cho menu/filter)
        $cate = Category::query()
            ->select(['id', 'name', 'slug', 'description', 'parent_id', 'status'])
            ->where('status', 1)
            ->get();

        // Nếu không có bất kỳ filter nào => trang danh sách sản phẩm dạng "mỗi danh mục 1 dòng, 4 sản phẩm mới"
        $hasAnyFilter = $searchQuery || $slug || $categoryInput || $minPrice || $maxPrice || $sort !== 'newest';
        if (!$hasAnyFilter) {
            $groupedCategories = Category::query()
                ->select(['id', 'name', 'slug'])
                ->where('status', 1)
                ->orderBy('name')
                ->with(['products' => function ($q) {
                    $q->where('status', 1)
                        ->orderBy('created_at', 'desc')
                        ->take(4);
                }])
                ->get()
                ->filter(function ($category) {
                    return $category->products->count() > 0;
                });

            $title = 'Sản phẩm';

            return view('client.products.index', [
                'groupedCategories' => $groupedCategories,
                'cate' => $cate,
                'title' => $title,
                'searchQuery' => null,
                'selectedCategory' => null,
                'products' => collect(), // để view không lỗi khi check
            ]);
        }

        // Khởi tạo truy vấn sản phẩm khi có filter

        // Khởi tạo truy vấn sản phẩm
        $query = Product::query()
            ->select(['id', 'name', 'slug', 'price', 'image', 'status', 'description', 'created_at', 'category_id'])
            ->where('status', 1);

        // ✅ Lọc theo danh mục nếu có
        $selectedCategory = null;
        if ($slug) {
            $selectedCategory = Category::where('slug', $slug)->firstOrFail();
        } elseif ($categoryInput) {
            $selectedCategory = is_numeric($categoryInput)
                ? Category::find($categoryInput)
                : Category::where('slug', $categoryInput)->first();
            if (!$selectedCategory && is_numeric($categoryInput)) {
                abort(404);
            }
        }

        if ($selectedCategory) {
            $categoryIds = $this->getDescendantCategoryIds((int) $selectedCategory->id);
            $query->whereIn('category_id', $categoryIds);
        }

        // ✅ Lọc theo từ khóa nếu người dùng nhập
        if ($searchQuery) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('name', 'LIKE', "%{$searchQuery}%")
                    ->orWhere('description', 'LIKE', "%{$searchQuery}%")
                    ->orWhere('slug', 'LIKE', "%{$searchQuery}%");
            });
        }

        // ✅ Lọc theo giá slider
        if ($minPrice && $maxPrice) {
            $query->whereBetween('price', [$minPrice, $maxPrice]);
        } elseif ($minPrice) {
            $query->where('price', '>=', $minPrice);
        } elseif ($maxPrice) {
            $query->where('price', '<=', $maxPrice);
        }

        // ✅ Sắp xếp
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // ✅ Phân trang
        $products = $query->paginate(12)->withQueryString();

        $title = $searchQuery
            ? 'Kết quả tìm kiếm'
            : ($selectedCategory->name ?? 'Sản phẩm');

        return view('client.products.index', compact(
            'products',
            'searchQuery',
            'cate',
            'selectedCategory',
            'title'
        ));

        return view('client.search', compact('products', 'searchQuery', 'cate', 'selectedCategory'));

    }

    private function getDescendantCategoryIds(int $rootCategoryId)
    {
        $allIds = [$rootCategoryId];
        $queue = [$rootCategoryId];

        while (!empty($queue)) {
            $currentId = array_shift($queue);
            $children = Category::where('parent_id', $currentId)->pluck('id')->all();

            foreach ($children as $childId) {
                if (!in_array($childId, $allIds, true)) {
                    $allIds[] = $childId;
                    $queue[] = $childId;
                }
            }
        }

        return $allIds;
    }
}
