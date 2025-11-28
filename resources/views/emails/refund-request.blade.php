<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yêu cầu {{ $refundTypeLabel }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
        }
        .info-box {
            background-color: white;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #007bff;
            border-radius: 4px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 12px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Yêu cầu {{ $refundTypeLabel }}</h2>
    </div>
    
    <div class="content">
        <p>Xin chào <strong>{{ $user->name }}</strong>,</p>
        
        <p>Chúng tôi đã nhận được yêu cầu {{ strtolower($refundTypeLabel) }} của bạn cho đơn hàng <strong>#{{ $order->order_code }}</strong>.</p>
        
        <div class="info-box">
            <h3 style="margin-top: 0;">Thông tin đơn hàng:</h3>
            <p><strong>Mã đơn hàng:</strong> #{{ $order->order_code }}</p>
            <p><strong>Số tiền hoàn:</strong> {{ number_format($refund->refund_amount, 0, ',', '.') }} đ</p>
            <p><strong>Lý do:</strong> {{ $refund->cancel_reason }}</p>
            @if($refund->reason_description)
                <p><strong>Mô tả:</strong> {{ $refund->reason_description }}</p>
            @endif
        </div>

        <div class="info-box">
            <h3 style="margin-top: 0;">Thông tin tài khoản nhận hoàn tiền:</h3>
            <p><strong>Ngân hàng:</strong> {{ $refund->bank_name }}</p>
            <p><strong>Số tài khoản:</strong> {{ $refund->bank_account }}</p>
            <p><strong>Tên chủ tài khoản:</strong> {{ $refund->account_holder }}</p>
        </div>

        <p>Yêu cầu của bạn đang được xử lý. Chúng tôi sẽ thông báo cho bạn ngay khi hoàn tất việc hoàn tiền.</p>
        
        <p>Thời gian xử lý dự kiến: <strong>3-5 ngày làm việc</strong></p>
        
        <p>Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi.</p>
        
        <p>Trân trọng,<br>
        <strong>Đội ngũ {{ config('app.name') }}</strong></p>
    </div>
    
    <div class="footer">
        <p>Email này được gửi tự động, vui lòng không trả lời.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Tất cả các quyền được bảo lưu.</p>
    </div>
</body>
</html>

