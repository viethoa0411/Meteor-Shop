<?php

namespace App\Http\Controllers\Client\Contact;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Services\NotificationService;

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

            // Gửi email thông báo cho admin
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
                \Log::error('Lỗi gửi email admin: ' . $e->getMessage());
            }

            // Gửi email xác nhận cho khách hàng
            try {
                Mail::raw("Chào {$contact->name},\n\n" .
                    "Cảm ơn bạn đã quan tâm và đăng ký tư vấn thiết kế tại Meteor Shop.\n" .
                    "Chúng tôi đã nhận được thông tin của bạn và sẽ liên hệ lại trong thời gian sớm nhất.\n\n" .
                    "Thông tin đã đăng ký:\n" .
                    "Họ tên: {$contact->name}\n" .
                    "Số điện thoại: {$contact->phone}\n\n" .
                    "Trân trọng,\nĐội ngũ Meteor Shop",
                    function ($message) use ($contact) {
                        $message->to($contact->email)
                            ->subject('Xác nhận đăng ký tư vấn thiết kế - Meteor Shop');
                    });
            } catch (\Exception $e) {
                \Log::error('Lỗi gửi email xác nhận khách hàng: ' . $e->getMessage());
            }

            // Tạo thông báo cho admin về liên hệ mới
            try {
                NotificationService::notifyNewContact($contact);
            } catch (\Exception $e) {
                // Không dừng flow nếu tạo notification thất bại
                \Log::error('Error creating contact notification: ' . $e->getMessage());
            }

            return redirect()->route('client.home')->with('success', 'Đăng ký tư vấn thành công! Chúng tôi đã gửi email xác nhận cho bạn.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Lỗi gửi liên hệ: ' . $e->getMessage());
        }
    }
}
