<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; padding: 20px; background: #f8f9fa;">
    <div style="max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
        <h2 style="color: #f97316;">Xin chào {{ $name }},</h2>
        <p>Có yêu cầu thay đổi thông tin tài khoản <strong>Admin</strong> của bạn trên hệ thống <strong>Meteor Shop</strong>.</p>
        <p>Mã xác nhận OTP:</p>
        <h1 style="font-size: 48px; color: #f97316; text-align: center; letter-spacing: 10px; background: #fff3e0; padding: 20px; border-radius: 12px;">
            {{ $otp }}
        </h1>
        <p>Mã có hiệu lực trong <strong>10 phút</strong>.</p>
        <p style="color: red;">Nếu bạn KHÔNG yêu cầu thay đổi, vui lòng liên hệ quản trị viên ngay lập tức!</p>
        <hr>
        <p>Trân trọng,<br>Đội ngũ Meteor Shop</p>
    </div>
</body>
</html>