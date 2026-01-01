<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactInfo;
use Illuminate\Http\Request;

class ContactInfoController extends Controller
{
    public function index()
    {
        $contact = ContactInfo::firstOrCreate([]);
        return view('admin.contact-info.index', compact('contact'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'zalo_link' => 'nullable|url',
            'messenger_link' => 'nullable|url',
            'phone_number' => 'nullable|string|max:20',
            'show_zalo' => 'boolean',
            'show_messenger' => 'boolean',
            'show_phone' => 'boolean',
        ]);

        $contact = ContactInfo::firstOrCreate([]);
        $contact->update([
            'zalo_link' => $request->zalo_link,
            'messenger_link' => $request->messenger_link,
            'phone_number' => $request->phone_number,
            'show_zalo' => $request->show_zalo ?? false,
            'show_messenger' => $request->show_messenger ?? false,
            'show_phone' => $request->show_phone ?? false,
        ]);

        return back()->with('success', 'Cập nhật thông tin liên hệ thành công!');
    }
}