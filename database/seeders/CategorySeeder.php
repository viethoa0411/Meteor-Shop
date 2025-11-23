<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    
    public function run(): void
    {
        $items = [
            'Nội thất phòng khách',
            'Nội thất phòng ngủ' ,
            'Nội thất phòng ăn' ,
            'Nội thất văn phòng' ,
            'Nội thất ngoài trời' 
        ];

        foreach ($items as $name) {
            Category::updateOrCreate(
                ['slug' => Str::slug($name) ],
                [
                    'name'              => $name,
                    'description'       => 'Danh mục demo' .$name,
                    'parent_id'         => null,
                    'status'            => 'active',
                ]
            );
        }
    }
}

