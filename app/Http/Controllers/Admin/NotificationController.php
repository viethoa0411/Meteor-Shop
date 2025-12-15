<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Build filter query - Reusable method
     */
    private function buildFilterQuery(Request $request)
    {
        $query = Notification::where('user_id', Auth::id());

        // Type filter - Validate với database
        if ($request->filled('type')) {
            $type = trim($request->type);
            if (!empty($type)) {
                // Validate type exists in database
                $validTypes = Notification::where('user_id', Auth::id())
                    ->distinct()
                    ->pluck('type')
                    ->toArray();
                
                if (in_array($type, $validTypes)) {
                    $query->where('type', $type);
                }
            }
        }

        // Level filter - Validate với whitelist
        if ($request->filled('level')) {
            $level = trim($request->level);
            $validLevels = ['info', 'success', 'warning', 'danger'];
            if (in_array($level, $validLevels)) {
                $query->where('level', $level);
            }
        }

        // Status filter
        if ($request->filled('status')) {
            $status = trim($request->status);
            if ($status === 'read') {
                $query->where('is_read', true);
            } elseif ($status === 'unread') {
                $query->where('is_read', false);
            }
        }

        // Search filter - Sanitize và escape
        if ($request->filled('search')) {
            $search = trim($request->search);
            if (!empty($search) && strlen($search) <= 255) {
                // Escape special characters for LIKE query
                $search = str_replace(['%', '_'], ['\%', '\_'], $search);
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('message', 'like', "%{$search}%");
                });
            }
        }

        // Date filters - Validate format và date hợp lệ
        if ($request->filled('date_from')) {
            $dateFrom = trim($request->date_from);
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom)) {
                try {
                    $date = \Carbon\Carbon::createFromFormat('Y-m-d', $dateFrom);
                    $query->whereDate('created_at', '>=', $dateFrom);
                } catch (\Exception $e) {
                    // Invalid date, skip filter
                    Log::warning('Invalid date_from format: ' . $dateFrom);
                }
            }
        }

        if ($request->filled('date_to')) {
            $dateTo = trim($request->date_to);
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo)) {
                try {
                    $date = \Carbon\Carbon::createFromFormat('Y-m-d', $dateTo);
                    $query->whereDate('created_at', '<=', $dateTo);
                } catch (\Exception $e) {
                    // Invalid date, skip filter
                    Log::warning('Invalid date_to format: ' . $dateTo);
                }
            }
        }

        // Validate date range
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $dateFrom = trim($request->date_from);
            $dateTo = trim($request->date_to);
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom) && 
                preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo)) {
                try {
                    $from = \Carbon\Carbon::createFromFormat('Y-m-d', $dateFrom);
                    $to = \Carbon\Carbon::createFromFormat('Y-m-d', $dateTo);
                    
                    if ($from->gt($to)) {
                        throw new \Exception('Ngày bắt đầu không thể lớn hơn ngày kết thúc');
                    }
                } catch (\Exception $e) {
                    throw $e;
                }
            }
        }

        return $query;
    }

    /**
     * Display notification index page
     */
    public function index(Request $request)
    {
        try {
            // Build filter query
            $query = $this->buildFilterQuery($request);
            $query->orderBy('created_at', 'desc');

            // Pagination with proper query string preservation
            $notifications = $query->paginate(20)->withQueryString();
            
            // Ensure pagination works correctly - redirect if page doesn't exist
            if ($notifications->isEmpty() && $notifications->currentPage() > 1) {
                return redirect()->route('admin.notifications.index', $request->except('page'));
            }

            // Statistics - Áp dụng CÙNG filters với notifications query
            $statsQuery = $this->buildFilterQuery($request);
            
            $stats = $statsQuery->selectRaw('
                    COUNT(*) as total,
                    SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as unread,
                    SUM(CASE WHEN is_read = 1 THEN 1 ELSE 0 END) as `read`
                ')
                ->first();

            $statsByType = $statsQuery->clone()
                ->select('type', DB::raw('count(*) as count'))
                ->groupBy('type')
                ->pluck('count', 'type');

            $statsByLevel = $statsQuery->clone()
                ->select('level', DB::raw('count(*) as count'))
                ->groupBy('level')
                ->pluck('count', 'level');

            $stats = [
                'total' => $stats->total ?? 0,
                'unread' => $stats->unread ?? 0,
                'read' => $stats->read ?? 0,
                'by_type' => $statsByType,
                'by_level' => $statsByLevel,
            ];

            // Available filters - Lấy từ database
            $types = Notification::where('user_id', Auth::id())
                ->distinct()
                ->pluck('type')
                ->sort()
                ->values();

            $levels = ['info', 'success', 'warning', 'danger'];

            return view('admin.notifications.index', compact(
                'notifications',
                'stats',
                'types',
                'levels'
            ));
        } catch (\Exception $e) {
            Log::error('Error loading notifications page: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            $errorMessage = 'Có lỗi xảy ra khi tải trang.';
            if (str_contains($e->getMessage(), 'Ngày bắt đầu')) {
                $errorMessage = $e->getMessage();
            }
            
            return redirect()->route('admin.notifications.index')
                ->with('error', $errorMessage);
        }
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead($id)
    {
        try {
            $notification = Notification::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $notification->markAsRead();

            return response()->json([
                'success' => true,
                'message' => 'Đã đánh dấu đã đọc',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông báo',
            ], 404);
        }
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread($id)
    {
        try {
            $notification = Notification::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $notification->markAsUnread();

            return response()->json([
                'success' => true,
                'message' => 'Đã đánh dấu chưa đọc',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông báo',
            ], 404);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        try {
            Notification::where('user_id', Auth::id())
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Đã đánh dấu tất cả đã đọc',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra',
            ], 500);
        }
    }

    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        try {
            $notification = Notification::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa thông báo',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông báo',
            ], 404);
        }
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:read,unread,delete',
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ]);

        try {
            $query = Notification::where('user_id', Auth::id())
                ->whereIn('id', $request->ids);

            switch ($request->action) {
                case 'read':
                    $query->update([
                        'is_read' => true,
                        'read_at' => now(),
                    ]);
                    $message = 'Đã đánh dấu đã đọc';
                    break;

                case 'unread':
                    $query->update([
                        'is_read' => false,
                        'read_at' => null,
                    ]);
                    $message = 'Đã đánh dấu chưa đọc';
                    break;

                case 'delete':
                    $query->delete();
                    $message = 'Đã xóa thông báo';
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra',
            ], 500);
        }
    }

    /**
     * Get unread count
     */
    public function getUnreadCount()
    {
        try {
            $count = Notification::where('user_id', Auth::id())
                ->where('is_read', false)
                ->count();

            return response()->json([
                'success' => true,
                'count' => $count,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'count' => 0,
            ]);
        }
    }

    /**
     * Save filter to session
     */
    public function saveFilter(Request $request)
    {
        try {
            $filter = $request->only(['search', 'type', 'level', 'status', 'date_from', 'date_to']);
            session(['notification_filter_' . Auth::id() => $filter]);

            return response()->json([
                'success' => true,
                'message' => 'Đã lưu bộ lọc',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lưu bộ lọc',
            ], 500);
        }
    }
}
