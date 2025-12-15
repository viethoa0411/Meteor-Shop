<?php

namespace App\Helpers;

class ShippingHelper
{
    /**
     * Danh sách tỉnh/thành phố miền Bắc
     */
    private static $northernProvinces = [
        'Hà Nội', 'Hà Nội', 'Hanoi',
        'Hải Phòng', 'Hải Phòng', 'Haiphong',
        'Hải Dương', 'Hải Dương', 'Haiduong',
        'Hưng Yên', 'Hưng Yên', 'Hungyen',
        'Hà Nam', 'Hà Nam', 'Hanam',
        'Nam Định', 'Nam Định', 'Namdinh',
        'Thái Bình', 'Thái Bình', 'Thaibinh',
        'Ninh Bình', 'Ninh Bình', 'Ninhbinh',
        'Bắc Ninh', 'Bắc Ninh', 'Bacninh',
        'Bắc Giang', 'Bắc Giang', 'Bacgiang',
        'Quảng Ninh', 'Quảng Ninh', 'Quangninh',
        'Lào Cai', 'Lào Cai', 'Laocai',
        'Yên Bái', 'Yên Bái', 'Yenbai',
        'Tuyên Quang', 'Tuyên Quang', 'Tuyenquang',
        'Lạng Sơn', 'Lạng Sơn', 'Langson',
        'Cao Bằng', 'Cao Bằng', 'Caobang',
        'Bắc Kạn', 'Bắc Kạn', 'Backan',
        'Thái Nguyên', 'Thái Nguyên', 'Thainguyen',
        'Phú Thọ', 'Phú Thọ', 'Phutho',
        'Vĩnh Phúc', 'Vĩnh Phúc', 'Vinhphuc',
        'Điện Biên', 'Điện Biên', 'Dienbien',
        'Lai Châu', 'Lai Châu', 'Laichau',
        'Sơn La', 'Sơn La', 'Sonla',
        'Hòa Bình', 'Hòa Bình', 'Hoabinh',
    ];

    /**
     * Normalize tên tỉnh để so sánh (loại bỏ dấu, prefix, khoảng trắng)
     * 
     * @param string|null $name
     * @return string
     */
    private static function normalizeProvinceName(?string $name): string
    {
        if (!$name) {
            return '';
        }
        
        $normalized = mb_strtolower(trim($name), 'UTF-8');
        
        // Loại bỏ dấu tiếng Việt
        $normalized = self::removeVietnameseAccents($normalized);
        
        // Loại bỏ prefix "Tỉnh", "Thành phố", "TP.", "T.P."
        $normalized = preg_replace('/^(tinh|thanh pho|tp\.?|t\.p\.?)\s+/i', '', $normalized);
        
        // Chuẩn hóa khoảng trắng
        $normalized = preg_replace('/\s+/', ' ', $normalized);
        
        return trim($normalized);
    }

    /**
     * Loại bỏ dấu tiếng Việt
     * 
     * @param string $str
     * @return string
     */
    private static function removeVietnameseAccents(string $str): string
    {
        $accents = [
            'à' => 'a', 'á' => 'a', 'ạ' => 'a', 'ả' => 'a', 'ã' => 'a',
            'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ậ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a',
            'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ặ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a',
            'è' => 'e', 'é' => 'e', 'ẹ' => 'e', 'ẻ' => 'e', 'ẽ' => 'e',
            'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ệ' => 'e', 'ể' => 'e', 'ễ' => 'e',
            'ì' => 'i', 'í' => 'i', 'ị' => 'i', 'ỉ' => 'i', 'ĩ' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ọ' => 'o', 'ỏ' => 'o', 'õ' => 'o',
            'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ộ' => 'o', 'ổ' => 'o', 'ỗ' => 'o',
            'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ợ' => 'o', 'ở' => 'o', 'ỡ' => 'o',
            'ù' => 'u', 'ú' => 'u', 'ụ' => 'u', 'ủ' => 'u', 'ũ' => 'u',
            'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ự' => 'u', 'ử' => 'u', 'ữ' => 'u',
            'ỳ' => 'y', 'ý' => 'y', 'ỵ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y',
            'đ' => 'd',
        ];
        
        return strtr($str, $accents);
    }

    /**
     * Kiểm tra xem tỉnh/thành phố có phải là miền Bắc không
     * 
     * @param string|null $provinceName
     * @return bool
     */
    public static function isNorthernProvince(?string $provinceName): bool
    {
        if (!$provinceName) {
            return false;
        }
        
        $normalized = self::normalizeProvinceName($provinceName);
        $normalizedNoSpace = str_replace(' ', '', $normalized);
        
        // Danh sách tỉnh miền Bắc đã normalize
        $northernProvincesNormalized = [
            'ha noi', 'hanoi',
            'hai phong', 'haiphong',
            'hai duong', 'haiduong',
            'hung yen', 'hungyen',
            'ha nam', 'hanam',
            'nam dinh', 'namdinh',
            'thai binh', 'thaibinh',
            'ninh binh', 'ninhbinh',
            'bac ninh', 'bacninh',
            'bac giang', 'bacgiang',
            'quang ninh', 'quangninh',
            'lao cai', 'laocai',
            'yen bai', 'yenbai',
            'tuyen quang', 'tuyenquang',
            'lang son', 'langson',
            'cao bang', 'caobang',
            'bac kan', 'backan',
            'thai nguyen', 'thainguyen',
            'phu tho', 'phutho',
            'vinh phuc', 'vinhphuc',
            'dien bien', 'dienbien',
            'lai chau', 'laichau',
            'son la', 'sonla',
            'hoa binh', 'hoabinh',
        ];
        
        // Check trực tiếp
        if (in_array($normalized, $northernProvincesNormalized) || 
            in_array($normalizedNoSpace, $northernProvincesNormalized)) {
            return true;
        }
        
        // Check nếu tên tỉnh chứa tên tỉnh miền Bắc
        foreach ($northernProvincesNormalized as $northernName) {
            if (strpos($normalized, $northernName) !== false || 
                strpos($normalizedNoSpace, str_replace(' ', '', $northernName)) !== false) {
                return true;
            }
        }
        
        return false;
    }
}


