<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingDistance extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shipping_distances';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>`
     */
    protected $fillable = [
        'province_name',
        'district_name',
        'distance_km',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'distance_km' => 'decimal:2',
    ];

    /**
     * Tìm khoảng cách theo tỉnh và quận/huyện
     * So sánh linh hoạt để tìm trùng tên quận/huyện
     * 
     * @param string $provinceName Tên tỉnh/thành phố
     * @param string $districtName Tên quận/huyện/thị xã
     * @return float|null Khoảng cách (km) hoặc null nếu không tìm thấy
     */
    public static function findDistance(string $provinceName, string $districtName): ?float
    {
        // Normalize tên để so sánh
        $normalizedProvince = self::normalizeName($provinceName);
        $normalizedDistrict = self::normalizeName($districtName);
        
        // Tìm chính xác trước
        $distance = self::where('province_name', $provinceName)
            ->where('district_name', $districtName)
            ->first();
        
        if ($distance) {
            return (float) $distance->distance_km;
        }
        
        // Tìm với normalize (so sánh linh hoạt)
        $distances = self::all();
        foreach ($distances as $d) {
            $dbProvince = self::normalizeName($d->province_name);
            $dbDistrict = self::normalizeName($d->district_name);
            
            // So sánh tỉnh và quận/huyện đã normalize
            if ($dbProvince === $normalizedProvince && $dbDistrict === $normalizedDistrict) {
                return (float) $d->distance_km;
            }
            
            // So sánh một phần (nếu tên quận/huyện chứa nhau)
            if ($dbProvince === $normalizedProvince) {
                if (strpos($dbDistrict, $normalizedDistrict) !== false || 
                    strpos($normalizedDistrict, $dbDistrict) !== false) {
                    return (float) $d->distance_km;
                }
            }
        }
        
        return null;
    }
    
    /**
     * Normalize tên địa danh để so sánh (loại bỏ dấu, prefix, khoảng trắng)
     * 
     * @param string $name
     * @return string
     */
    private static function normalizeName(string $name): string
    {
        if (empty($name)) {
            return '';
        }
        
        // Loại bỏ khoảng trắng thừa và chuyển về lowercase
        $normalized = mb_strtolower(trim($name), 'UTF-8');
        
        // Loại bỏ dấu tiếng Việt
        $normalized = self::removeVietnameseAccents($normalized);
        
        // Loại bỏ prefix "Quận", "Huyện", "Thị xã", "Thành phố", "Tỉnh"
        $normalized = preg_replace('/^(quan|huyen|thi xa|thanh pho|tinh|tp\.?)\s+/i', '', $normalized);
        
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
            'À' => 'A', 'Á' => 'A', 'Ạ' => 'A', 'Ả' => 'A', 'Ã' => 'A',
            'Â' => 'A', 'Ầ' => 'A', 'Ấ' => 'A', 'Ậ' => 'A', 'Ẩ' => 'A', 'Ẫ' => 'A',
            'Ă' => 'A', 'Ằ' => 'A', 'Ắ' => 'A', 'Ặ' => 'A', 'Ẳ' => 'A', 'Ẵ' => 'A',
            'È' => 'E', 'É' => 'E', 'Ẹ' => 'E', 'Ẻ' => 'E', 'Ẽ' => 'E',
            'Ê' => 'E', 'Ề' => 'E', 'Ế' => 'E', 'Ệ' => 'E', 'Ể' => 'E', 'Ễ' => 'E',
            'Ì' => 'I', 'Í' => 'I', 'Ị' => 'I', 'Ỉ' => 'I', 'Ĩ' => 'I',
            'Ò' => 'O', 'Ó' => 'O', 'Ọ' => 'O', 'Ỏ' => 'O', 'Õ' => 'O',
            'Ô' => 'O', 'Ồ' => 'O', 'Ố' => 'O', 'Ộ' => 'O', 'Ổ' => 'O', 'Ỗ' => 'O',
            'Ơ' => 'O', 'Ờ' => 'O', 'Ớ' => 'O', 'Ợ' => 'O', 'Ở' => 'O', 'Ỡ' => 'O',
            'Ù' => 'U', 'Ú' => 'U', 'Ụ' => 'U', 'Ủ' => 'U', 'Ũ' => 'U',
            'Ư' => 'U', 'Ừ' => 'U', 'Ứ' => 'U', 'Ự' => 'U', 'Ử' => 'U', 'Ữ' => 'U',
            'Ỳ' => 'Y', 'Ý' => 'Y', 'Ỵ' => 'Y', 'Ỷ' => 'Y', 'Ỹ' => 'Y',
            'Đ' => 'D',
        ];
        
        return strtr($str, $accents);
    }

    /**
     * Tìm khoảng cách theo tỉnh (trả về khoảng cách trung bình nếu có nhiều quận/huyện)
     * 
     * @param string $provinceName Tên tỉnh/thành phố
     * @return float|null Khoảng cách trung bình (km) hoặc null nếu không tìm thấy
     */
    public static function findDistanceByProvince(string $provinceName): ?float
    {
        $distances = self::where('province_name', $provinceName)->get();
        
        if ($distances->isEmpty()) {
            return null;
        }

        $totalDistance = $distances->sum('distance_km');
        return (float) ($totalDistance / $distances->count());
    }

    /**
     * Tạo hoặc cập nhật khoảng cách
     * 
     * @param string $provinceName Tên tỉnh/thành phố
     * @param string $districtName Tên quận/huyện/thị xã
     * @param float $distanceKm Khoảng cách (km)
     * @return self
     */
    public static function createOrUpdate(string $provinceName, string $districtName, float $distanceKm): self
    {
        return self::updateOrCreate(
            [
                'province_name' => $provinceName,
                'district_name' => $districtName,
            ],
            [
                'distance_km' => $distanceKm,
            ]
        );
    }
}
