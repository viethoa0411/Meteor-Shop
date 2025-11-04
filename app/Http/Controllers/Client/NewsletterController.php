<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $data = $request->validate(['email' => 'required|email']);
        $token = Str::random(40);
        $subscription = NewsletterSubscription::updateOrCreate(
            ['email' => $data['email']],
            ['token' => $token]
        );
        // TODO: send mail confirm if mail configured
        // Mail::to($data['email'])->send(new NewsletterConfirmMail($subscription));
        return back()->with('success', 'Vui lòng kiểm tra email để xác nhận đăng ký.');
    }

    public function confirm(string $token)
    {
        $sub = NewsletterSubscription::where('token', $token)->firstOrFail();
        $sub->update(['confirmed_at' => now()]);
        return redirect()->route('client.home')->with('success', 'Đăng ký nhận tin thành công.');
    }
}


