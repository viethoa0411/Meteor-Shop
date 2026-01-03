<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Services\GoogleMapsService;
use App\Models\ShippingDistance;

class ShippingSetting extends Model
{
    protected $fillable = [
        'origin_address',
        'origin_city',
        'origin_district',
        'origin_ward',
        'base_fee',
        'fee_per_km',
        'default_distance_km',
        'inner_city_fee',
        'outer_city_fee',
        'other_province_fee',
        'first_length_price',
        'next_length_price',
        'first_width_price',
        'next_width_price',
        'first_height_price',
        'next_height_price',
        'first_weight_price',
        'next_weight_price',
        'length_block_cm',
        'width_block_cm',
        'height_block_cm',
        'weight_block_kg',
        'express_surcharge_type',
        'express_surcharge_value',
        'fast_surcharge_type',
        'fast_surcharge_value',
        'express_label',
        'fast_label',
        'installation_fee',
        'same_order_discount_percent',
        'same_product_discount_percent',
        'volume_price_per_m3',
        'min_shipping_fee',
        'conversion_factor',
        'price_per_km_per_ton',
        'free_km_first',
        'labor_fee_type',
        'labor_fee_value',
    ];

    protected $casts = [
        'base_fee' => 'decimal:0',
        'fee_per_km' => 'decimal:0',
        'default_distance_km' => 'decimal:2',
        'inner_city_fee' => 'decimal:0',
        'outer_city_fee' => 'decimal:0',
        'other_province_fee' => 'decimal:0',
        'first_length_price' => 'decimal:0',
        'next_length_price' => 'decimal:0',
        'first_width_price' => 'decimal:0',
        'next_width_price' => 'decimal:0',
        'first_height_price' => 'decimal:0',
        'next_height_price' => 'decimal:0',
        'first_weight_price' => 'decimal:0',
        'next_weight_price' => 'decimal:0',
        'length_block_cm' => 'integer',
        'width_block_cm' => 'integer',
        'height_block_cm' => 'integer',
        'weight_block_kg' => 'integer',
        'express_surcharge_value' => 'decimal:0',
        'fast_surcharge_value' => 'decimal:0',
        'installation_fee' => 'decimal:0',
        'same_order_discount_percent' => 'decimal:2',
        'same_product_discount_percent' => 'decimal:2',
        'volume_price_per_m3' => 'decimal:0',
        'min_shipping_fee' => 'decimal:0',
        'conversion_factor' => 'integer',
        'price_per_km_per_ton' => 'decimal:0',
        'free_km_first' => 'decimal:2',
        'labor_fee_value' => 'decimal:2',
    ];

    /**
     * Lấy cài đặt vận chuyển (singleton pattern)
     */
    public static function getSettings()
    {
        $settings = self::first();
        
        if (!$settings) {
            // Tạo cài đặt mặc định nếu chưa có
            $settings = self::create([
                'origin_city' => 'Hà Nội',
                'origin_district' => 'Cầu Giấy',
                'origin_ward' => 'Dịch Vọng',
                'origin_address' => '123 Đường ABC',
                'base_fee' => 30000,
                'fee_per_km' => 5000,
                'free_shipping_threshold' => 10000000, // 10 triệu
                'inner_city_fee' => 30000,
                'outer_city_fee' => 50000,
                'other_province_fee' => 80000,
                'first_length_price' => 10000,
                'next_length_price' => 5000,
                'first_width_price' => 8000,
                'next_width_price' => 4000,
                'first_height_price' => 8000,
                'next_height_price' => 4000,
                'first_weight_price' => 15000,
                'next_weight_price' => 7000,
                'length_block_cm' => 200,
                'width_block_cm' => 200,
                'height_block_cm' => 200,
                'weight_block_kg' => 10,
                'express_surcharge_type' => 'percent',
                'express_surcharge_value' => 20,
                'fast_surcharge_type' => 'percent',
                'fast_surcharge_value' => 40,
                'express_label' => 'Giao hàng nhanh',
                'fast_label' => 'Giao hàng hỏa tốc',
                'free_shipping_enabled' => true,
                'installation_fee' => 0,
                'same_order_discount_percent' => 0,
                'same_product_discount_percent' => 0,
                'volume_price_per_m3' => 5000,
                'min_shipping_fee' => 30000,
                'conversion_factor' => 5000,
                'price_per_km_per_ton' => 17000,
                'free_km_first' => 10.0,
                'labor_fee_type' => 'percent',
                'labor_fee_value' => 10.0,
            ]);
        }
        
        return $settings;
    }

    /**
     * Tính phí vận chuyển cho danh sách hàng hóa dựa trên kích thước, cân nặng và phương thức.
     *
     * @param array  $items  Danh sách item dạng:
     *  [
     *      [
     *          'length_cm' => 120,
     *          'width_cm'  => 60,
     *          'height_cm' => 40,
     *          'weight_kg' => 5,
     *          'quantity'  => 2,
     *      ],
     *  ]
     * @param string $method    standard|express|fast
     * @param float  $subtotal  Tổng giá trị đơn để áp dụng miễn phí ship (nếu có)
     * @param string|null $destinationCity  Tỉnh/thành phố đích (để tính khoảng cách)
     * @param string|null $destinationDistrict  Quận/huyện đích (để tính khoảng cách)
     * @param string|null $destinationWard  Phường/xã đích (để tính khoảng cách, tùy chọn)
     * @param string|null $destinationAddress  Địa chỉ chi tiết đích (để tính khoảng cách, tùy chọn)
     *
     * @return array ['total' => float, 'standard_fee' => float, 'surcharge' => float]
     */
    public function calculateShippingFee(array $items, string $method = 'standard', float $subtotal = 0, ?string $destinationCity = null, ?string $destinationDistrict = null, ?string $destinationWard = null, ?string $destinationAddress = null): array
    {
        // Normalize và trim địa chỉ đích
        $destinationCity = $destinationCity ? trim($destinationCity) : null;
        $destinationDistrict = $destinationDistrict ? trim($destinationDistrict) : null;
        
        // Tính khoảng cách (km) - sử dụng Google Maps API nếu được bật
        $distanceKm = $this->calculateDistance($destinationCity, $destinationDistrict, $destinationWard, $destinationAddress);
        
        Log::debug('Shipping: Tính khoảng cách', [
            'origin_city' => $this->origin_city,
            'origin_district' => $this->origin_district,
            'origin_ward' => $this->origin_ward,
            'origin_address' => $this->origin_address,
            'destination_city' => $destinationCity,
            'destination_district' => $destinationDistrict,
            'destination_ward' => $destinationWard,
            'destination_address' => $destinationAddress,
            'distance_km' => $distanceKm,
            'google_maps_enabled' => config('services.google_maps.enabled', false),
        ]);
        
        // Log để debug
        Log::info('Shipping: Bắt đầu tính phí', [
            'items_count' => count($items),
            'items' => $items,
            'distance_km' => $distanceKm,
            'subtotal' => $subtotal,
        ]);

        // Kiểm tra điều kiện miễn phí vận chuyển
        if ($this->free_shipping_enabled && $subtotal >= $this->free_shipping_threshold) {
            Log::info('Shipping: Đủ điều kiện miễn phí vận chuyển', [
                'subtotal' => $subtotal,
                'threshold' => $this->free_shipping_threshold,
            ]);
            
            return [
                'standard_fee' => 0,
                'surcharge' => 0,
                'total' => 0,
                'is_free' => true,
            ];
        }

        // Tính phí vận chuyển theo block cm và cân nặng cho TỪNG item, sau đó cộng dồn
        $totalStandardFee = 0;
        $lengthBlockCm = (int)($this->length_block_cm ?? 200);
        $widthBlockCm = (int)($this->width_block_cm ?? 200);
        $heightBlockCm = (int)($this->height_block_cm ?? 200);
        $weightBlockKg = (int)($this->weight_block_kg ?? 10);

        foreach ($items as $item) {
            $quantity = max(1, (int)($item['quantity'] ?? 1));
            $lengthCm = (float)($item['length_cm'] ?? 0);
            $widthCm = (float)($item['width_cm'] ?? 0);
            $heightCm = (float)($item['height_cm'] ?? 0);
            $weightKg = (float)($item['weight_kg'] ?? 0);

            // Tính phí kích thước và cân nặng cho 1 item (chưa nhân với km và quantity)
            $itemFee = $this->calculateDimensionAndWeightFee(
                $lengthCm,
                $widthCm,
                $heightCm,
                $weightKg,
                $lengthBlockCm,
                $widthBlockCm,
                $heightBlockCm,
                $weightBlockKg
            );

            // Tính phí ban đầu cho 1 sản phẩm (đã nhân với km)
            // F = phí vận chuyển 1 sản phẩm đầu tiên
            $itemFeePerUnit = $itemFee * $distanceKm;

            // Áp dụng giảm giá khi mua nhiều sản phẩm cùng loại (quantity > 1)
            // Công thức: Nếu q = 1: Phí = F
            //            Nếu q ≥ 2: Phí = F + (q - 1) × F × r
            // Trong đó: r = tỷ lệ tính phí cho sản phẩm tiếp theo
            //           r = (100 - same_product_discount_percent) / 100
            if ($quantity > 1 && $this->same_product_discount_percent > 0) {
                // Tính tỷ lệ r: nếu giảm 50% thì r = 0.5, nếu giảm 10% thì r = 0.9
                $r = (100 - (float)$this->same_product_discount_percent) / 100;
                
                // Công thức: F + (q - 1) × F × r
                $itemTotalFee = $itemFeePerUnit + (($quantity - 1) * $itemFeePerUnit * $r);
                
                Log::debug('Shipping: Áp dụng giảm giá sản phẩm cùng loại', [
                    'quantity' => $quantity,
                    'item_fee_per_unit' => $itemFeePerUnit,
                    'discount_percent' => $this->same_product_discount_percent,
                    'rate_r' => $r,
                    'item_total_fee' => $itemTotalFee,
                    'formula' => "F + (q - 1) × F × r = {$itemFeePerUnit} + ({$quantity} - 1) × {$itemFeePerUnit} × {$r}",
                ]);
            } else {
                // Không có giảm giá hoặc quantity = 1: nhân với số lượng như bình thường
                $itemTotalFee = $itemFeePerUnit * $quantity;
            }
            
            $totalStandardFee += $itemTotalFee;

            Log::debug('Shipping: Tính phí cho item', [
                'length_cm' => $lengthCm,
                'width_cm' => $widthCm,
                'height_cm' => $heightCm,
                'weight_kg' => $weightKg,
                'quantity' => $quantity,
                'item_fee' => $itemFee,
                'item_fee_per_unit' => $itemFeePerUnit,
                'distance_km' => $distanceKm,
                'item_total_fee' => $itemTotalFee,
            ]);
        }

        // Phí tiêu chuẩn (đã áp dụng giảm giá sản phẩm cùng loại cho từng item trong vòng lặp)
        $standardFee = $totalStandardFee;

        // Áp dụng phí tối thiểu
        $minShippingFee = (float)($this->min_shipping_fee ?? 30000);
        $standardFee = max($standardFee, $minShippingFee);

        Log::info('Shipping: Tổng phí tiêu chuẩn (sau khi áp dụng giảm giá sản phẩm cùng loại)', [
            'total_standard_fee' => $standardFee,
            'items_count' => count($items),
            'distance_km' => $distanceKm,
        ]);

        $surcharge = $this->calculateSurcharge($method, $standardFee);

        return [
            'standard_fee' => $standardFee,
            'surcharge' => $surcharge,
            'total' => max(0, $standardFee + $surcharge),
        ];
    }

    /**
     * Tính phí kích thước và cân nặng (CHƯA nhân với km và quantity)
     * 
     * @param float $lengthCm
     * @param float $widthCm
     * @param float $heightCm
     * @param float $weightKg
     * @return float Phí kích thước + cân nặng (chưa nhân với km)
     */
    private function calculateDimensionAndWeightFee(
        float $lengthCm,
        float $widthCm,
        float $heightCm,
        float $weightKg,
        int $lengthBlockCm = 200,
        int $widthBlockCm = 200,
        int $heightBlockCm = 200,
        int $weightBlockKg = 10
    ): float {
        // Tính số block cho từng chiều (dựa vào cấu hình)
        $lengthBlocks = max(0, $lengthCm / $lengthBlockCm);
        $widthBlocks = max(0, $widthCm / $widthBlockCm);
        $heightBlocks = max(0, $heightCm / $heightBlockCm);
        $weightKg = max(0, $weightKg);

        // Tính phí kích thước và cân nặng
        $lengthFee = $this->calculateDimensionFee($lengthBlocks, (float)$this->first_length_price, (float)$this->next_length_price);
        $widthFee = $this->calculateDimensionFee($widthBlocks, (float)$this->first_width_price, (float)$this->next_width_price);
        $heightFee = $this->calculateDimensionFee($heightBlocks, (float)$this->first_height_price, (float)$this->next_height_price);
        
        // Cân nặng: block kg đầu + block kg tiếp theo
        $weightFee = 0;
        if ($weightKg > 0) {
            $weightBlocks = max(1, ceil($weightKg / $weightBlockKg));
            $extraWeightBlocks = max(0, $weightBlocks - 1);
            $weightFee = (float)$this->first_weight_price + ($extraWeightBlocks * (float)$this->next_weight_price);
        }

        // Tổng phí kích thước + cân nặng
        return $lengthFee + $widthFee + $heightFee + $weightFee;
    }

    /**
     * Tính phí tiêu chuẩn cho 1 item (nhân với quantity).
     * Logic mới: Tính tổng phí kích thước + cân nặng trước, sau đó nhân với km.
     * 
     * Ví dụ: Chiều dài 3 mét, mét đầu 8000, mét tiếp theo 10000
     * Phí chiều dài = 8000 + (3-1)*10000 = 8000 + 20000 = 28000
     * Sau đó: Tổng phí = (Chiều dài + Chiều rộng + Chiều cao + Cân nặng) × km
     */
    public function calculateStandardFeeForItem(
        float $lengthCm,
        float $widthCm,
        float $heightCm,
        float $weightKg,
        int $quantity = 1,
        float $distanceKm = 1.0
    ): float {
        // Tất cả kích thước: 200cm đầu + 200cm tiếp theo (thay vì 1m đầu + 1m tiếp theo)
        $length200cmBlocks = max(0, $lengthCm / 200);
        $width200cmBlocks = max(0, $widthCm / 200);
        $height200cmBlocks = max(0, $heightCm / 200);
        $weightKg = max(0, $weightKg);
        
        // Đảm bảo distanceKm tối thiểu là 1km
        $distanceKm = max(1.0, $distanceKm);

        // BƯỚC 1: Tính phí kích thước và cân nặng (CHƯA nhân với km)
        // Chiều dài: 200cm đầu + 200cm tiếp theo
        $lengthFee = $this->calculateDimensionFee($length200cmBlocks, (float)$this->first_length_price, (float)$this->next_length_price);
        
        // Chiều rộng: 200cm đầu + 200cm tiếp theo
        $widthFee = $this->calculateDimensionFee($width200cmBlocks, (float)$this->first_width_price, (float)$this->next_width_price);
        
        // Chiều cao: 200cm đầu + 200cm tiếp theo
        $heightFee = $this->calculateDimensionFee($height200cmBlocks, (float)$this->first_height_price, (float)$this->next_height_price);
        
        // Cân nặng: 10kg đầu + 10kg tiếp theo (thay vì 1kg đầu + 1kg tiếp theo)
        $weightFee = 0;
        $weight10kgBlocks = 0;
        if ($weightKg > 0) {
            // 10kg đầu tiên: first_weight_price
            // Mỗi 10kg tiếp theo: next_weight_price
            // Ví dụ: 25kg, 10kg đầu 15000, mỗi 10kg tiếp theo 7000
            // Số khối 10kg: ceil(25/10) = 3
            // Phí = 15000 + (3-1)*7000 = 15000 + 14000 = 29000
            $weight10kgBlocks = max(1, ceil($weightKg / 10));
            $extraWeightBlocks = max(0, $weight10kgBlocks - 1);
            $weightFee = (float)$this->first_weight_price + ($extraWeightBlocks * (float)$this->next_weight_price);
        }

        // BƯỚC 2: Tổng phí kích thước + cân nặng (CHƯA nhân với km)
        $totalDimensionAndWeightFee = $lengthFee + $widthFee + $heightFee + $weightFee;

        // Log để debug
        Log::info('Shipping: Tính phí kích thước và cân nặng', [
            'length_cm' => $lengthCm,
            'width_cm' => $widthCm,
            'height_cm' => $heightCm,
            'weight_kg' => $weightKg,
            'length_200cm_blocks' => $length200cmBlocks,
            'width_200cm_blocks' => $width200cmBlocks,
            'height_200cm_blocks' => $height200cmBlocks,
            'weight_10kg_blocks' => $weight10kgBlocks,
            'length_fee' => $lengthFee,
            'width_fee' => $widthFee,
            'height_fee' => $heightFee,
            'weight_fee' => $weightFee,
            'total_dimension_weight_fee' => $totalDimensionAndWeightFee,
            'distance_km' => $distanceKm,
            'quantity' => $quantity,
            'first_length_price' => $this->first_length_price,
            'next_length_price' => $this->next_length_price,
            'first_width_price' => $this->first_width_price,
            'next_width_price' => $this->next_width_price,
            'first_height_price' => $this->first_height_price,
            'next_height_price' => $this->next_height_price,
            'first_weight_price' => $this->first_weight_price,
            'next_weight_price' => $this->next_weight_price,
        ]);

        // Kiểm tra nếu tổng phí = 0 (có thể do không có dữ liệu kích thước/cân nặng hoặc giá phí = 0)
        if ($totalDimensionAndWeightFee <= 0) {
            Log::warning('Shipping: Tổng phí kích thước và cân nặng = 0', [
                'length_cm' => $lengthCm,
                'width_cm' => $widthCm,
                'height_cm' => $heightCm,
                'weight_kg' => $weightKg,
            ]);
        }

        // BƯỚC 3: Nhân với số km
        $fee = $totalDimensionAndWeightFee * $distanceKm;

        // BƯỚC 4: Nhân với số lượng
        $finalFee = max(0, $fee * max(1, $quantity));
        
        Log::info('Shipping: Phí cuối cùng cho item', [
            'final_fee' => $finalFee,
            'fee_before_quantity' => $fee,
            'quantity' => $quantity,
        ]);
        
        return $finalFee;
    }

    /**
     * Tính phụ phí cho phương thức nhanh/hỏa tốc dựa trên phí vận chuyển đã tính.
     * 
     * Logic:
     * - Nếu là %: Phụ phí = Phí vận chuyển × %
     * - Nếu là số tiền: Phụ phí = Số tiền admin cài
     */
    public function calculateSurcharge(string $method, float $standardFee): float
    {
        if ($standardFee <= 0 || $method === 'standard') {
            return 0;
        }

        $type = $method === 'fast' ? $this->fast_surcharge_type : $this->express_surcharge_type;
        $value = $method === 'fast' ? $this->fast_surcharge_value : $this->express_surcharge_value;

        // Nếu là %: Phụ phí = Phí vận chuyển × %
        if ($type === 'percent') {
            return round(($standardFee * (float)$value) / 100);
        }

        // Nếu là số tiền: Phụ phí = Số tiền admin cài
        return max(0, (float)$value);
    }

    /**
     * Helper tính phí theo chiều dài/chiều rộng/chiều cao.
     */
    private function calculateDimensionFee(float $meters, float $firstPrice, float $nextPrice): float
    {
        if ($meters <= 0) {
            return 0;
        }

        $extraUnit = max(0, ceil($meters - 1));
        return $firstPrice + ($extraUnit * $nextPrice);
    }

    /**
     * Normalize tên địa danh để so sánh (loại bỏ dấu, prefix, khoảng trắng)
     * 
     * @param string|null $name
     * @return string
     */
    private function normalizeLocationName(?string $name): string
    {
        if (!$name) {
            return '';
        }
        
        // Loại bỏ khoảng trắng thừa và chuyển về lowercase
        $normalized = mb_strtolower(trim($name), 'UTF-8');
        
        // Loại bỏ dấu tiếng Việt
        $normalized = $this->removeVietnameseAccents($normalized);
        
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
    private function removeVietnameseAccents(string $str): string
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
     * Tính khoảng cách (km) từ kho gốc đến địa chỉ đích.
     * Ưu tiên lấy từ bảng shipping_distances, sau đó Google Maps API, cuối cùng là ước tính.
     * 
     * @param string|null $destinationCity  Tỉnh/thành phố đích
     * @param string|null $destinationDistrict  Quận/huyện đích
     * @param string|null $destinationWard  Phường/xã đích (tùy chọn)
     * @param string|null $destinationAddress  Địa chỉ chi tiết đích (tùy chọn)
     * @return float  Khoảng cách (km)
     */
    private function calculateDistance(?string $destinationCity, ?string $destinationDistrict, ?string $destinationWard = null, ?string $destinationAddress = null): float
    {
        $defaultDistance = (float)($this->default_distance_km ?? 10.0);

        // Nếu không có thông tin đích, dùng khoảng cách mặc định
        if (!$destinationCity || trim($destinationCity) === '') {
            Log::warning('Shipping: Không có thông tin địa chỉ đích, dùng khoảng cách mặc định', [
                'default_distance_km' => $defaultDistance
            ]);
            return $defaultDistance;
        }

        $originCity = $this->origin_city ?? '';
        $originDistrict = $this->origin_district ?? '';
        $originWard = $this->origin_ward ?? '';
        $originAddress = $this->origin_address ?? '';

        // Nếu không có địa chỉ gốc, dùng khoảng cách mặc định
        if (!$originCity || trim($originCity) === '') {
            Log::warning('Shipping: Không có địa chỉ kho hàng, dùng khoảng cách mặc định', [
                'default_distance_km' => $defaultDistance
            ]);
            return $defaultDistance;
        }

        // ƯU TIÊN 1: Tìm khoảng cách từ bảng shipping_distances
        if ($destinationDistrict && trim($destinationDistrict) !== '') {
            $distance = ShippingDistance::findDistance($destinationCity, $destinationDistrict);
            if ($distance !== null && $distance > 0) {
                Log::info('Shipping: Sử dụng khoảng cách từ bảng shipping_distances', [
                    'province' => $destinationCity,
                    'district' => $destinationDistrict,
                    'distance_km' => $distance,
                ]);
                return (float) $distance;
            }
            
            Log::warning('Shipping: Không tìm thấy khoảng cách trong bảng shipping_distances', [
                'province' => $destinationCity,
                'district' => $destinationDistrict,
            ]);

            // Nếu không tìm thấy theo quận/huyện, thử tìm theo tỉnh (trung bình)
            $avgDistance = ShippingDistance::findDistanceByProvince($destinationCity);
            if ($avgDistance !== null && $avgDistance > 0) {
                Log::info('Shipping: Sử dụng khoảng cách trung bình theo tỉnh từ bảng shipping_distances', [
                    'province' => $destinationCity,
                    'distance_km' => $avgDistance,
                ]);
                return (float) $avgDistance;
            }

            // Nếu không tìm thấy trong database, sử dụng khoảng cách mặc định
            Log::warning('Shipping: Không tìm thấy trong database, sử dụng khoảng cách mặc định', [
                'province' => $destinationCity,
                'district' => $destinationDistrict,
                'default_distance_km' => $defaultDistance,
            ]);
            return $defaultDistance;
        }

        // ƯU TIÊN 2: Sử dụng Google Maps API nếu được bật
        $googleMapsEnabled = config('services.google_maps.enabled', false);
        
        if ($googleMapsEnabled) {
            // Sử dụng Google Maps API để tính khoảng cách thực tế
            try {
                $googleMaps = new GoogleMapsService();
                
                // Xây dựng địa chỉ đầy đủ
                $originFullAddress = $this->buildFullAddress($originAddress, $originWard, $originDistrict, $originCity);
                $destinationFullAddress = $this->buildFullAddress($destinationAddress, $destinationWard, $destinationDistrict, $destinationCity);
                
                $result = $googleMaps->calculateDistance($originFullAddress, $destinationFullAddress);
                
                if ($result && isset($result['distance_km'])) {
                    Log::info('Shipping: Sử dụng Google Maps API', [
                        'origin' => $originFullAddress,
                        'destination' => $destinationFullAddress,
                        'distance_km' => $result['distance_km'],
                        'duration_minutes' => $result['duration_minutes'] ?? null,
                    ]);
                    return (float) $result['distance_km'];
                }
                
                // Nếu Google Maps API lỗi, fallback về ước tính
                Log::warning('Shipping: Google Maps API lỗi, dùng phương pháp ước tính');
            } catch (\Exception $e) {
                Log::error('Shipping: Google Maps API exception', [
                    'message' => $e->getMessage(),
                ]);
                // Fallback về ước tính
            }
        }

        // FALLBACK: Phương pháp ước tính (khi không có trong bảng và không dùng Google Maps)
        return $this->estimateDistance($originCity, $originDistrict, $destinationCity, $destinationDistrict);
    }

    /**
     * Xây dựng địa chỉ đầy đủ từ các thành phần
     * 
     * @param string|null $address Địa chỉ chi tiết
     * @param string|null $ward Phường/Xã
     * @param string|null $district Quận/Huyện
     * @param string|null $city Tỉnh/Thành phố
     * @return string
     */
    private function buildFullAddress(?string $address, ?string $ward, ?string $district, ?string $city): string
    {
        $parts = array_filter([
            trim($address ?? ''),
            trim($ward ?? ''),
            trim($district ?? ''),
            trim($city ?? ''),
        ]);
        
        return implode(', ', $parts);
    }

    /**
     * Ước tính khoảng cách dựa trên so sánh địa danh (phương pháp cũ)
     * 
     * @param string $originCity
     * @param string|null $originDistrict
     * @param string $destinationCity
     * @param string|null $destinationDistrict
     * @return float
     */
    private function estimateDistance(string $originCity, ?string $originDistrict, string $destinationCity, ?string $destinationDistrict): float
    {
        // Normalize tên để so sánh chính xác
        $normalizedOriginCity = $this->normalizeLocationName($originCity);
        $normalizedDestinationCity = $this->normalizeLocationName($destinationCity);
        
        // Kiểm tra cùng tỉnh/thành phố
        if ($normalizedOriginCity && $normalizedDestinationCity && 
            $normalizedOriginCity === $normalizedDestinationCity) {
            
            // Cùng tỉnh/thành phố
            if ($originDistrict && $destinationDistrict) {
                $normalizedOriginDistrict = $this->normalizeLocationName($originDistrict);
                $normalizedDestinationDistrict = $this->normalizeLocationName($destinationDistrict);
                
                // Nếu cùng quận/huyện, khoảng cách ngắn (5-15km)
                if ($normalizedOriginDistrict && $normalizedDestinationDistrict &&
                    $normalizedOriginDistrict === $normalizedDestinationDistrict) {
                    Log::info('Shipping: Cùng quận/huyện (ước tính)', [
                        'origin' => $originCity . ', ' . $originDistrict,
                        'destination' => $destinationCity . ', ' . $destinationDistrict,
                        'distance' => 10
                    ]);
                    return 10.0; // Cùng quận/huyện: ~10km
                }
            }
            
            // Khác quận/huyện nhưng cùng tỉnh: 20-50km
            Log::info('Shipping: Cùng tỉnh, khác quận/huyện (ước tính)', [
                'origin' => $originCity . ', ' . ($originDistrict ?? 'N/A'),
                'destination' => $destinationCity . ', ' . ($destinationDistrict ?? 'N/A'),
                'distance' => 30
            ]);
            return 30.0;
        }

        // Khác tỉnh/thành phố: ước tính 50-200km (mặc định 100km)
        Log::info('Shipping: Khác tỉnh/thành phố (ước tính)', [
            'origin' => $originCity,
            'destination' => $destinationCity,
            'distance' => 100
        ]);
        return 100.0;
    }
}

