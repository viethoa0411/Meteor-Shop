<?php

namespace App\Http\Controllers\Admin\Contact;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;

class ContactController extends Controller
{
    /**
     * Hiển thị danh sách liên hệ tư vấn thiết kế
     */
    public function index(Request $request)
    {
        $query = Contact::query();

        // Lọc theo trạng thái
        $status = $request->get('status', 'all');
        if ($status !== 'all') {
            if ($status === 'processed') {
                $query->where('status', 'processed');
            } elseif ($status === 'pending') {
                $query->where('status', 'pending');
            }
        }

        // Sắp xếp theo ID giảm dần và phân trang
        $contacts = $query->orderBy('id', 'desc')->paginate(10);

        // Giữ lại từ khóa tìm kiếm và status khi chuyển trang
        $contacts->appends($request->only(['keyword', 'status']));

        return view('admin.contact.list', compact('contacts'));
    }

}
