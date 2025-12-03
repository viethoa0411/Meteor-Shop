<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Cài đặt ví - Thông tin ngân hàng admin
 */
class WalletSetting extends Model
{
    protected $fillable = [
        'bank_name',
        'bank_account',
        'account_holder',
        'bank_code',
        'support_phone',
        'support_email',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Mapping tên ngân hàng -> mã VietQR
    const BANK_CODES = [
        'Vietcombank' => 'VCB',
        'Techcombank' => 'TCB',
        'BIDV' => 'BIDV',
        'VietinBank' => 'CTG',
        'Agribank' => 'AGR',
        'ACB' => 'ACB',
        'MB Bank' => 'MB',
        'VPBank' => 'VPB',
        'TPBank' => 'TPB',
        'Sacombank' => 'STB',
        'HDBank' => 'HDB',
        'VIB' => 'VIB',
        'SHB' => 'SHB',
        'Eximbank' => 'EIB',
        'MSB' => 'MSB',
        'OCB' => 'OCB',
        'SeABank' => 'SEAB',
        'LienVietPostBank' => 'LPB',
        'KienLongBank' => 'KLB',
    ];

    /**
     * Lấy settings (singleton)
     */
    public static function getSettings(): self
    {
        $settings = self::first();
        if (!$settings) {
            $settings = self::create([
                'bank_name' => 'MB Bank',
                'bank_account' => '0123456789',
                'account_holder' => 'NGUYEN VAN A',
                'bank_code' => 'MB',
                'support_phone' => '0123456789',
            ]);
        }
        return $settings;
    }

    /**
     * Tạo QR code URL cho VietQR
     */
    public function generateQrUrl(float $amount, string $content): string
    {
        $template = 'compact2';
        $addInfo = urlencode($content);
        $accountName = urlencode($this->account_holder);
        
        return "https://img.vietqr.io/image/{$this->bank_code}-{$this->bank_account}-{$template}.png?amount={$amount}&addInfo={$addInfo}&accountName={$accountName}";
    }
}

