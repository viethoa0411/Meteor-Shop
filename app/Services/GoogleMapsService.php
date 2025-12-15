<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GoogleMapsService
{
    private $apiKey;
    private $baseUrl = 'https://maps.googleapis.com/maps/api';

    public function __construct()
    {
        $this->apiKey = config('services.google_maps.api_key');
    }

    /**
     * Tính khoảng cách thực tế giữa 2 địa chỉ sử dụng Google Distance Matrix API
     * 
     * @param string $origin Địa chỉ gốc (ví dụ: "123 Đường ABC, Quận X, Hà Nội")
     * @param string $destination Địa chỉ đích (ví dụ: "456 Đường XYZ, Quận Y, Hà Nội")
     * @param string $mode Phương thức di chuyển: driving, walking, bicycling, transit (mặc định: driving)
     * @return array|null ['distance_km' => float, 'duration_minutes' => int, 'status' => string] hoặc null nếu lỗi
     */
    public function calculateDistance(string $origin, string $destination, string $mode = 'driving'): ?array
    {
        if (!$this->apiKey) {
            Log::warning('Google Maps API key chưa được cấu hình');
            return null;
        }

        // Tạo cache key dựa trên origin, destination và mode
        $cacheKey = 'google_maps_distance_' . md5($origin . $destination . $mode);
        
        // Kiểm tra cache (cache 24 giờ)
        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            Log::debug('Google Maps: Sử dụng kết quả từ cache', ['cache_key' => $cacheKey]);
            return $cached;
        }

        try {
            $url = "{$this->baseUrl}/distancematrix/json";
            
            $response = Http::timeout(10)->get($url, [
                'origins' => $origin,
                'destinations' => $destination,
                'mode' => $mode,
                'language' => 'vi',
                'key' => $this->apiKey,
            ]);

            if (!$response->successful()) {
                Log::error('Google Maps API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $data = $response->json();

            // Kiểm tra status của response
            if ($data['status'] !== 'OK') {
                Log::warning('Google Maps API returned error status', [
                    'status' => $data['status'],
                    'error_message' => $data['error_message'] ?? 'Unknown error',
                ]);
                return null;
            }

            // Lấy kết quả từ elements
            if (empty($data['rows']) || empty($data['rows'][0]['elements'])) {
                Log::warning('Google Maps API: Không có kết quả');
                return null;
            }

            $element = $data['rows'][0]['elements'][0];

            // Kiểm tra status của element
            if ($element['status'] !== 'OK') {
                Log::warning('Google Maps API element status', [
                    'status' => $element['status'],
                    'origin' => $origin,
                    'destination' => $destination,
                ]);
                return null;
            }

            // Lấy khoảng cách (mét) và thời gian (giây)
            $distanceMeters = $element['distance']['value'] ?? 0;
            $durationSeconds = $element['duration']['value'] ?? 0;

            // Chuyển đổi sang km và phút
            $distanceKm = round($distanceMeters / 1000, 2);
            $durationMinutes = round($durationSeconds / 60, 1);

            $result = [
                'distance_km' => $distanceKm,
                'duration_minutes' => $durationMinutes,
                'distance_text' => $element['distance']['text'] ?? '',
                'duration_text' => $element['duration']['text'] ?? '',
                'status' => $element['status'],
            ];

            // Cache kết quả trong 24 giờ
            Cache::put($cacheKey, $result, now()->addHours(24));

            Log::info('Google Maps: Tính khoảng cách thành công', [
                'origin' => $origin,
                'destination' => $destination,
                'distance_km' => $distanceKm,
                'duration_minutes' => $durationMinutes,
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('Google Maps API exception', [
                'message' => $e->getMessage(),
                'origin' => $origin,
                'destination' => $destination,
            ]);
            return null;
        }
    }

    /**
     * Geocode địa chỉ thành tọa độ (lat, lng)
     * 
     * @param string $address Địa chỉ cần geocode
     * @return array|null ['lat' => float, 'lng' => float] hoặc null nếu lỗi
     */
    public function geocode(string $address): ?array
    {
        if (!$this->apiKey) {
            return null;
        }

        $cacheKey = 'google_maps_geocode_' . md5($address);
        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        try {
            $url = "{$this->baseUrl}/geocode/json";
            
            $response = Http::timeout(10)->get($url, [
                'address' => $address,
                'language' => 'vi',
                'key' => $this->apiKey,
            ]);

            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();

            if ($data['status'] !== 'OK' || empty($data['results'])) {
                return null;
            }

            $location = $data['results'][0]['geometry']['location'];
            $result = [
                'lat' => $location['lat'],
                'lng' => $location['lng'],
            ];

            Cache::put($cacheKey, $result, now()->addDays(30));
            return $result;
        } catch (\Exception $e) {
            Log::error('Google Maps Geocode exception', ['message' => $e->getMessage()]);
            return null;
        }
    }
}


