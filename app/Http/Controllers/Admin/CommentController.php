<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\ReviewReply;
use App\Models\ReviewAuditLog;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CommentController extends Controller
{
    /**
     * Danh sách tất cả bình luận
     */
    public function index(Request $request)
    {
        $query = Review::with(['product.images', 'user', 'reports', 'replies.admin'])
            ->withCount('helpfulVotes');

        $this->applyFilters($query, $request);

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if ($sortBy === 'helpful_votes_count') {
            $query->orderBy('helpful_votes_count', $sortOrder)
                  ->orderBy('created_at', 'desc'); // Secondary sort
        } else {
        $query->orderBy($sortBy, $sortOrder);
        }

        // Items per page
        $perPage = $request->get('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 25, 50, 100]) ? $perPage : 20;

        $reviews = $query->paginate($perPage)->withQueryString();
        $products = Product::select('id', 'name')->orderBy('name')->get();

        // Thống kê tổng quan để không phải query trực tiếp trong view
        $stats = [
            'total' => Review::count(),
            'pending' => Review::pending()->count(),
            'approved' => Review::approved()->count(),
            'reported' => Review::reported()->count(),
        ];

        return view('admin.comments.index', compact('reviews', 'products', 'stats'));
    }

    /**
     * Quick view modal
     */
    public function quickView($id)
    {
        $review = Review::with(['product.images', 'user', 'reports.user', 'replies.admin'])
            ->withCount('helpfulVotes')
            ->findOrFail($id);

        $html = view('admin.comments.partials.quick-view', compact('review'))->render();
        
        // Get images for lightbox
        $images = [];
        if ($review->images && is_array($review->images)) {
            foreach ($review->images as $img) {
                $images[] = asset('storage/' . $img);
            }
        }

        return response()->json([
            'html' => $html,
            'images' => $images
        ]);
    }

    /**
     * Chi tiết bình luận
     */
    public function show($id)
    {
        $review = Review::with(['product.images', 'user', 'reports.user', 'replies.admin'])
            ->withCount('helpfulVotes')
            ->findOrFail($id);

        return view('admin.comments.show', compact('review'));
    }

    /**
     * Bình luận chờ duyệt
     */
    public function pending(Request $request)
    {
        $query = Review::with(['product.images', 'user'])
            ->withCount('helpfulVotes')
            ->pending();

        $this->applyFilters($query, $request, ['allow_status' => false]);

        $reviews = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.comments.pending', compact('reviews'));
    }

    /**
     * Bình luận bị report
     */
    public function reported(Request $request)
    {
        $query = Review::with(['product.images', 'user', 'reports.user'])
            ->withCount('helpfulVotes')
            ->reported()
            ->orderBy('reported_count', 'desc')
            ->orderBy('created_at', 'desc');

        $this->applyFilters($query, $request, ['allow_status' => false]);

        if ($request->filled('reason')) {
            $query->whereHas('reports', function($q) use ($request) {
                $q->where('reason', $request->reason);
            });
        }

        $reviews = $query->paginate(20);

        return view('admin.comments.reported', compact('reviews'));
    }

    /**
     * Phê duyệt bình luận
     */
    public function approve($id)
    {
        $review = Review::findOrFail($id);
        $oldStatus = $review->status;
        $review->status = 'approved';
        $review->save();

        // Log audit
        $this->logAudit($review, 'approve', 'Review approved by admin', [
            'old_status' => $oldStatus,
            'new_status' => 'approved'
        ]);

        // Update product rating
        $this->updateProductRating($review->product_id);

        return response()->json([
            'status' => 'success',
            'message' => 'Đã phê duyệt bình luận thành công'
        ]);
    }

    /**
     * Từ chối bình luận
     */
    public function reject($id)
    {
        $review = Review::findOrFail($id);
        $oldStatus = $review->status;
        $review->status = 'rejected';
        $review->save();

        // Log audit
        $this->logAudit($review, 'reject', 'Review rejected by admin', [
            'old_status' => $oldStatus,
            'new_status' => 'rejected'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Đã từ chối bình luận'
        ]);
    }

    /**
     * Ẩn bình luận
     */
    public function hide($id)
    {
        $review = Review::findOrFail($id);
        $oldStatus = $review->status;
        $review->status = 'hidden';
        $review->save();

        // Log audit
        $this->logAudit($review, 'hide', 'Review hidden by admin', [
            'old_status' => $oldStatus,
            'new_status' => 'hidden'
        ]);

        // Update product rating
        $this->updateProductRating($review->product_id);

        return response()->json([
            'status' => 'success',
            'message' => 'Đã ẩn bình luận'
        ]);
    }

    /**
     * Hiện bình luận
     */
    public function showComment($id)
    {
        $review = Review::findOrFail($id);
        $oldStatus = $review->status;
        $review->status = 'approved';
        $review->save();

        // Log audit
        $this->logAudit($review, 'show', 'Review shown by admin', [
            'old_status' => $oldStatus,
            'new_status' => 'approved'
        ]);

        // Update product rating
        $this->updateProductRating($review->product_id);

        return response()->json([
            'status' => 'success',
            'message' => 'Đã hiển thị bình luận'
        ]);
    }

    /**
     * Phê duyệt hàng loạt
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:reviews,id'
        ]);

        $reviews = Review::whereIn('id', $request->ids)->get();
        $productIds = [];

        foreach ($reviews as $review) {
            $oldStatus = $review->status;
            $review->status = 'approved';
            $review->save();
            
            // Log audit
            $this->logAudit($review, 'approve', 'Bulk approved by admin', [
                'old_status' => $oldStatus,
                'new_status' => 'approved'
            ]);
            
            $productIds[] = $review->product_id;
        }

        // Update ratings for affected products
        foreach (array_unique($productIds) as $productId) {
            $this->updateProductRating($productId);
        }

        return response()->json([
            'status' => 'success',
            'message' => "Đã phê duyệt {$reviews->count()} bình luận"
        ]);
    }

    /**
     * Từ chối hàng loạt
     */
    public function bulkReject(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:reviews,id'
        ]);

        $reviews = Review::whereIn('id', $request->ids)->get();
        foreach ($reviews as $review) {
            $oldStatus = $review->status;
            $review->status = 'rejected';
            $review->save();
            
            // Log audit
            $this->logAudit($review, 'reject', 'Bulk rejected by admin', [
                'old_status' => $oldStatus,
                'new_status' => 'rejected'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => "Đã từ chối {$reviews->count()} bình luận"
        ]);
    }

    /**
     * Ẩn hàng loạt
     */
    public function bulkHide(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:reviews,id'
        ]);

        $reviews = Review::whereIn('id', $request->ids)->get();
        $productIds = [];

        foreach ($reviews as $review) {
            $oldStatus = $review->status;
            $review->status = 'hidden';
            $review->save();
            
            // Log audit
            $this->logAudit($review, 'hide', 'Bulk hidden by admin', [
                'old_status' => $oldStatus,
                'new_status' => 'hidden'
            ]);
            
            $productIds[] = $review->product_id;
        }

        // Update ratings for affected products
        foreach (array_unique($productIds) as $productId) {
            $this->updateProductRating($productId);
        }

        return response()->json([
            'status' => 'success',
            'message' => "Đã ẩn {$reviews->count()} bình luận"
        ]);
    }

    /**
     * Xóa hàng loạt
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:reviews,id'
        ]);

        $reviews = Review::whereIn('id', $request->ids)->get();
        $productIds = [];

        foreach ($reviews as $review) {
            $oldStatus = $review->status;
            $productIds[] = $review->product_id;
            
            // Log audit before delete
            $this->logAudit($review, 'delete', 'Bulk deleted by admin', [
                'old_status' => $oldStatus,
                'product_id' => $review->product_id
            ]);
            
            $review->delete();
        }

        // Update ratings for affected products
        foreach (array_unique($productIds) as $productId) {
            $this->updateProductRating($productId);
        }

        return response()->json([
            'status' => 'success',
            'message' => "Đã xóa {$reviews->count()} bình luận"
        ]);
    }

    /**
     * Export reviews to Excel
     */
    public function export(Request $request)
    {
        $query = Review::with(['product', 'user']);

        $this->applyFilters($query, $request);

        $reviews = $query->orderBy('created_at', 'desc')->get();

        $filename = 'reviews_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($reviews) {
            $file = fopen('php://output', 'w');
            
            // BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, [
                'ID', 'Sản phẩm', 'User', 'Email', 'Rating', 'Nội dung', 
                'Trạng thái', 'Đã mua', 'Có ảnh', 'Số lần report', 'Ngày tạo'
            ]);

            // Data
            foreach ($reviews as $review) {
                fputcsv($file, [
                    $review->id,
                    $review->product->name ?? 'N/A',
                    $review->user->name ?? 'N/A',
                    $review->user->email ?? 'N/A',
                    $review->rating,
                    $review->content ?? $review->comment ?? '',
                    $review->status,
                    $review->is_verified_purchase ? 'Có' : 'Không',
                    ($review->images && count($review->images) > 0) ? 'Có' : 'Không',
                    $review->reported_count,
                    $review->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Trả lời bình luận (Admin reply)
     */
    public function reply(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        $review = Review::findOrFail($id);

        ReviewReply::create([
            'review_id' => $review->id,
            'admin_id' => auth()->id(),
            'content' => $request->content,
        ]);

        // Log audit
        $this->logAudit($review, 'reply', 'Admin replied to review', [
            'reply_content' => substr($request->content, 0, 100) // Store first 100 chars
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Đã gửi phản hồi thành công'
        ]);
    }

    /**
     * Xóa phản hồi của admin
     */
    public function deleteReply($reviewId, $replyId)
    {
        $review = Review::findOrFail($reviewId);
        $reply = ReviewReply::where('id', $replyId)
            ->where('review_id', $reviewId)
            ->firstOrFail();

        $replySnippet = Str::limit($reply->content ?? '', 100);
        $reply->delete();

        $this->logAudit($review, 'reply_delete', 'Admin deleted a reply', [
            'reply_id' => $replyId,
            'reply_content' => $replySnippet,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Đã xóa phản hồi'
        ]);
    }

    /**
     * Xóa nhiều phản hồi cùng lúc
     */
    public function bulkDeleteReplies(Request $request, $reviewId)
    {
        $request->validate([
            'reply_ids' => 'required|array|min:1',
            'reply_ids.*' => 'integer'
        ]);

        $review = Review::findOrFail($reviewId);

        $replies = ReviewReply::where('review_id', $reviewId)
            ->whereIn('id', $request->reply_ids)
            ->get();

        if ($replies->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy phản hồi để xóa'
            ], 404);
        }

        foreach ($replies as $reply) {
            $snippet = Str::limit($reply->content ?? '', 100);
            $replyId = $reply->id;
            $reply->delete();

            $this->logAudit($review, 'reply_delete', 'Admin deleted a reply (bulk)', [
                'reply_id' => $replyId,
                'reply_content' => $snippet,
                'bulk' => true
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Đã xóa ' . $replies->count() . ' phản hồi',
            'deleted_ids' => $replies->pluck('id')->values()
        ]);
    }

    /**
     * Xóa bình luận
     */
    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $productId = $review->product_id;
        $oldStatus = $review->status;
        
        // Log audit before delete
        $this->logAudit($review, 'delete', 'Review deleted by admin', [
            'old_status' => $oldStatus,
            'product_id' => $productId
        ]);
        
        $review->delete();

        // Update product rating
        $this->updateProductRating($productId);

        return response()->json([
            'status' => 'success',
            'message' => 'Đã xóa bình luận'
        ]);
    }

    /**
     * Cài đặt
     */
    public function settings()
    {
        $settings = [
            'auto_approve' => config('reviews.auto_approve', false),
            'auto_verify_buyer' => config('reviews.auto_verify_buyer', true),
            'allow_images' => config('reviews.allow_images', true),
            'max_images' => config('reviews.max_images', 5),
            'blacklist_keywords' => config('reviews.blacklist_keywords', []),
            'auto_hide_blacklist' => config('reviews.auto_hide_blacklist', false),
        ];

        // Convert array to string for textarea
        if (is_array($settings['blacklist_keywords'])) {
            $settings['blacklist_keywords'] = implode("\n", $settings['blacklist_keywords']);
        }

        return view('admin.comments.settings', compact('settings'));
    }

    /**
     * Lưu cài đặt
     */
    public function saveSettings(Request $request)
    {
        $request->validate([
            'auto_approve' => 'nullable|in:on,1',
            'auto_verify_buyer' => 'nullable|in:on,1',
            'allow_images' => 'nullable|in:on,1',
            'max_images' => 'required|integer|min:1|max:10',
            'blacklist_keywords' => 'nullable|string',
            'auto_hide_blacklist' => 'nullable|in:on,1',
        ]);

        try {
            // Save to config file
            $configPath = config_path('reviews.php');
            
            // Parse blacklist keywords
            $blacklistKeywords = [];
            if ($request->filled('blacklist_keywords')) {
                $keywords = explode("\n", $request->blacklist_keywords);
                $blacklistKeywords = array_filter(array_map('trim', $keywords), function($keyword) {
                    return !empty($keyword);
                });
            }

            $config = [
                'auto_approve' => $request->has('auto_approve'),
                'auto_verify_buyer' => $request->has('auto_verify_buyer'),
                'allow_images' => $request->has('allow_images'),
                'max_images' => (int)$request->input('max_images', 5),
                'blacklist_keywords' => array_values($blacklistKeywords),
                'auto_hide_blacklist' => $request->has('auto_hide_blacklist'),
            ];

            // Format config file properly
            $content = "<?php\n\n";
            $content .= "return [\n";
            $content .= "    'auto_approve' => " . ($config['auto_approve'] ? 'true' : 'false') . ",\n";
            $content .= "    'auto_verify_buyer' => " . ($config['auto_verify_buyer'] ? 'true' : 'false') . ",\n";
            $content .= "    'allow_images' => " . ($config['allow_images'] ? 'true' : 'false') . ",\n";
            $content .= "    'max_images' => " . $config['max_images'] . ",\n";
            $content .= "    'blacklist_keywords' => [\n";
            foreach ($config['blacklist_keywords'] as $keyword) {
                $content .= "        '" . addslashes($keyword) . "',\n";
            }
            $content .= "    ],\n";
            $content .= "    'auto_hide_blacklist' => " . ($config['auto_hide_blacklist'] ? 'true' : 'false') . ",\n";
            $content .= "];\n";

            // Check if directory exists and is writable
            if (!is_writable(config_path())) {
                return redirect()->route('admin.comments.settings')
                    ->with('error', 'Không có quyền ghi file cấu hình. Vui lòng kiểm tra quyền thư mục config.');
            }

            // Write to config file
            if (file_put_contents($configPath, $content) === false) {
                return redirect()->route('admin.comments.settings')
                    ->with('error', 'Không thể ghi file cấu hình. Vui lòng kiểm tra quyền file.');
            }

            // Clear config cache
            if (function_exists('opcache_reset')) {
                opcache_reset();
            }
            
            // Clear Laravel config cache
            \Illuminate\Support\Facades\Artisan::call('config:clear');

            return redirect()->route('admin.comments.settings')
                ->with('success', 'Đã lưu cài đặt thành công! Các thay đổi sẽ có hiệu lực ngay lập tức.');
        } catch (\Exception $e) {
            return redirect()->route('admin.comments.settings')
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Apply shared filters to review queries.
     */
    protected function applyFilters($query, Request $request, array $options = []): void
    {
        $allowStatusFilter = $options['allow_status'] ?? true;

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($allowStatusFilter && $request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('has_images')) {
            $query->withImages();
        }

        if ($request->filled('reported')) {
            $query->reported();
        }

        if ($request->filled('verified')) {
            $query->where('is_verified_purchase', filter_var($request->verified, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('content', 'LIKE', "%{$search}%")
                    ->orWhereHas('product', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
                    });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
    }

    /**
     * Log audit trail
     */
    protected function logAudit($review, $action, $notes = null, $metadata = [])
    {
        try {
            ReviewAuditLog::create([
                'review_id' => $review->id,
                'admin_id' => auth()->id(),
                'action' => $action,
                'old_status' => $review->getOriginal('status') ?? $review->status,
                'new_status' => $review->status,
                'notes' => $notes,
                'metadata' => $metadata,
            ]);
        } catch (\Exception $e) {
            // Log error but don't break the main flow
            Log::error('Failed to create audit log', [
                'review_id' => $review->id ?? null,
                'action' => $action,
                'error' => $e->getMessage()
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
            $product->rating_avg = $approvedReviews->avg('rating');
            $product->save();
        } else {
            $product->rating_avg = 0;
            $product->save();
        }
    }
}
