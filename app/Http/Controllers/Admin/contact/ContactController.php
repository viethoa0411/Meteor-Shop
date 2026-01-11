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

        // Tìm kiếm theo tên, email, sdt
        if ($request->has('keyword') && $request->keyword != '') {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('phone', 'like', "%{$keyword}%");
            });
        }

        // Sắp xếp theo ID giảm dần và phân trang
        $contacts = $query->orderBy('id', 'desc')->paginate(10);

        // Giữ lại từ khóa tìm kiếm và status khi chuyển trang
        $contacts->appends($request->only(['keyword', 'status']));

        return view('admin.contact.list', compact('contacts'));
    }
    /**
     * Hiển thị chi tiết liên hệ
     */
    public function show($id)
    {
        $contact = Contact::findOrFail($id);
        return view('admin.contact.show', compact('contact'));
    }
    /**
     * Hiển thị form chỉnh sửa liên hệ
     */
    public function edit($id)
    {
        $contact = Contact::findOrFail($id);
        return view('admin.contact.edit', compact('contact'));
    }
     /**
 * Cập nhật liên hệ
 */
public function update(Request $request, $id)
{
    $contact = Contact::findOrFail($id);

    // Chỉ validate trường status
    $validated = $request->validate([
        'status' => 'required|in:pending,processed',
    ]);

    // Nếu trạng thái không thay đổi thì không cho cập nhật
    if ($validated['status'] === $contact->status) {
        return back()->with('error', 'Trạng thái chưa thay đổi, không thể cập nhật!');
    }

    // Không cho phép chuyển từ đã xử lý về chưa xử lý
    if ($contact->status === 'processed' && $validated['status'] === 'pending') {
        return back()->with('error', 'Không thể chuyển trạng thái từ Đã Xử lý về Chưa Xử lý!');
    }

    try {
        $contact->update(['status' => $validated['status']]);
        return redirect()->route('admin.contacts.index')->with('success', 'Cập nhật trạng thái thành công!');
    } catch (\Exception $e) {
        return back()->withInput()->with('error', 'Lỗi cập nhật: ' . $e->getMessage());
    }
}
}
