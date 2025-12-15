<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ShippingDistanceTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    /**
     * Dữ liệu mẫu
     */
    public function array(): array
    {
        return [
            [
                'Hà Nội',
                'Quận Ba Đình',
                8.5,
            ],
            [
                'Hà Nội',
                'Quận Hoàn Kiếm',
                10.0,
            ],
            [
                'Hà Nội',
                'Quận Cầu Giấy',
                7.5,
            ],
            [
                'Hải Phòng',
                'Quận Hồng Bàng',
                105.0,
            ],
            [
                'Hải Phòng',
                'Quận Lê Chân',
                107.0,
            ],
        ];
    }

    /**
     * Tiêu đề cột
     */
    public function headings(): array
    {
        return [
            'tinh_thanh_pho',
            'quan_huyen',
            'khoang_cach_km',
        ];
    }

    /**
     * Định dạng style cho worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style cho header row
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * Độ rộng cột
     */
    public function columnWidths(): array
    {
        return [
            'A' => 25,  // tinh_thanh_pho
            'B' => 30,  // quan_huyen
            'C' => 20,  // khoang_cach_km
        ];
    }
}

