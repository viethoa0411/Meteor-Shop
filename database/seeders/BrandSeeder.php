<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Aurora Living',
                'description' => 'Nội thất cao cấp mang phong cách Bắc Âu sang trọng.',
                'logo' => 'brands/aurora.png',
                'status' => 'active',
            ],
            [
                'name' => 'Meteor Decor',
                'description' => 'Đơn vị thiết kế độc quyền của Meteor Shop.',
                'logo' => 'brands/meteor.png',
                'status' => 'active',
            ],
            [
                'name' => 'Urban Craft',
                'description' => 'Sản phẩm thủ công dành cho không gian hiện đại.',
                'logo' => 'brands/urban-craft.png',
                'status' => 'active',
            ],
            [
                'name' => 'EcoHome Studio',
                'description' => 'Thương hiệu ưu tiên vật liệu bền vững và thân thiện môi trường.',
                'logo' => 'brands/ecohome.png',
                'status' => 'inactive',
            ],
            [
                'name' => 'Nova Workspace',
                'description' => 'Giải pháp nội thất văn phòng linh hoạt.',
                'logo' => 'brands/nova.png',
                'status' => 'active',
            ],
        ];

        foreach ($brands as $brand) {
            Brand::updateOrCreate(
                ['slug' => Str::slug($brand['name'])],
                array_merge($brand, [
                    'slug' => Str::slug($brand['name']),
                ])
            );
        }
    }
}
