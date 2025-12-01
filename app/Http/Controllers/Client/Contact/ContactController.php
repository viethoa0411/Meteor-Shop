<?php

namespace App\Http\Controllers\Client\Contact;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Hiển thị form liên hệ
     */
    public function list()
    {
        // Lấy danh sách categories để truyền vào layout
        $cate = Category::query()
            ->select(['name', 'slug', 'description', 'parent_id', 'status'])
            ->where('status', 1)
            ->get();

        return view('client.contact.list', compact('cate'));
    }

    /**
     * Lưu liên hệ và gửi email
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
        ], [
            'name.required' => 'Vui lòng nhập dữ liệu.',
            'name.string' => 'Tên phải là chuỗi ký tự.',
            'name.max' => 'Tên không được vượt quá 255 ký tự.',
            'address.string' => 'Địa chỉ phải là chuỗi ký tự.',
            'address.max' => 'Địa chỉ không được vượt quá 500 ký tự.',
            'email.required' => 'Vui lòng nhập dữ liệu.',
            'email.email' => 'Email không hợp lệ.',
            'email.max' => 'Email không được vượt quá 255 ký tự.',
            'phone.required' => 'Vui lòng nhập dữ liệu.',
            'phone.string' => 'Số điện thoại phải là chuỗi ký tự.',
            'phone.max' => 'Số điện thoại không được vượt quá 20 ký tự.',
        ]);

        try {
            // Lưu liên hệ vào database
            $contact = Contact::create([
                'name' => $validated['name'],
                'address' => $validated['address'] ?? null,
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'status' => 'pending',
                'contacted_at' => now(),
            ]);

            // Gửi email thông báo
            $adminEmail = env('MAIL_FROM_ADDRESS', 'admin@meteorshop.com');
            
            try {
                Mail::raw("Bạn có một liên hệ mới từ website:\n\n" .
                    "Tên: {$contact->name}\n" .
                    "Email: {$contact->email}\n" .
                    "Số điện thoại: {$contact->phone}\n" .
                    "Địa chỉ: " . ($contact->address ?? 'Không có') . "\n" .
                    "Ngày gửi: {$contact->contacted_at->format('d/m/Y H:i')}\n",
                    function ($message) use ($adminEmail, $contact) {
                        $message->to($adminEmail)
                            ->subject('Liên hệ mới từ ' . $contact->name);
                    });
            } catch (\Exception $e) {
                // Nếu gửi email thất bại, vẫn lưu liên hệ
                \Log::error('Lỗi gửi email liên hệ: ' . $e->getMessage());
            }

            return redirect()->route('client.contact.list')->with('success', 'Gửi liên hệ thành công! Chúng tôi sẽ phản hồi sớm nhất có thể.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Lỗi gửi liên hệ: ' . $e->getMessage());
        }
    }
}
