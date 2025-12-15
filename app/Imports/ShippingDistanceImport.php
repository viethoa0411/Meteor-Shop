<?php

namespace App\Imports;

use App\Models\ShippingDistance;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ShippingDistanceImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure, WithBatchInserts, WithChunkReading
{
    use Importable, SkipsFailures;

    protected $successCount = 0;
    protected $updateCount = 0;
    protected $skipCount = 0;

    /**
     * Xử lý từng row trong Excel
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Kiểm tra xem record đã tồn tại chưa
            $existing = ShippingDistance::where('province_name', $row['tinh_thanh_pho'])
                ->where('district_name', $row['quan_huyen'])
                ->first();

            if ($existing) {
                // Cập nhật nếu đã tồn tại
                $existing->update([
                    'distance_km' => $row['khoang_cach_km'],
                ]);
                $this->updateCount++;
            } else {
                // Tạo mới nếu chưa tồn tại
                ShippingDistance::create([
                    'province_name' => $row['tinh_thanh_pho'],
                    'district_name' => $row['quan_huyen'],
                    'distance_km' => $row['khoang_cach_km'],
                ]);
                $this->successCount++;
            }
        }
    }

    /**
     * Validation rules cho từng row
     */
    public function rules(): array
    {
        return [
            'tinh_thanh_pho' => [
                'required',
                'string',
                'max:255',
            ],
            'quan_huyen' => [
                'required',
                'string',
                'max:255',
            ],
            'khoang_cach_km' => [
                'required',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'tinh_thanh_pho.required' => 'Cột "Tỉnh/Thành phố" không được để trống',
            'tinh_thanh_pho.string' => 'Cột "Tỉnh/Thành phố" phải là chuỗi ký tự',
            'tinh_thanh_pho.max' => 'Cột "Tỉnh/Thành phố" không được vượt quá 255 ký tự',
            
            'quan_huyen.required' => 'Cột "Quận/Huyện" không được để trống',
            'quan_huyen.string' => 'Cột "Quận/Huyện" phải là chuỗi ký tự',
            'quan_huyen.max' => 'Cột "Quận/Huyện" không được vượt quá 255 ký tự',
            
            'khoang_cach_km.required' => 'Cột "Khoảng cách (km)" không được để trống',
            'khoang_cach_km.numeric' => 'Cột "Khoảng cách (km)" phải là số',
            'khoang_cach_km.min' => 'Cột "Khoảng cách (km)" phải lớn hơn hoặc bằng 0',
            'khoang_cach_km.max' => 'Cột "Khoảng cách (km)" không được vượt quá 999999.99',
        ];
    }

    /**
     * Batch size cho insert
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * Chunk size cho reading
     */
    public function chunkSize(): int
    {
        return 100;
    }

    /**
     * Lấy số lượng record thành công
     */
    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    /**
     * Lấy số lượng record được cập nhật
     */
    public function getUpdateCount(): int
    {
        return $this->updateCount;
    }

    /**
     * Lấy số lượng record bị skip
     */
    public function getSkipCount(): int
    {
        return count($this->failures());
    }
}

