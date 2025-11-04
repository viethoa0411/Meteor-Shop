<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Categories
        $cats = collect([
            'Gợi ý trang trí', 'Phong cách sống', 'Mẹo & thủ thuật'
        ])->map(function($name){
            return PostCategory::firstOrCreate([
                'slug' => Str::slug($name)
            ], [
                'name' => $name,
            ]);
        });

        // Tags
        $tagNames = ['minimal', 'scandinavian', 'modern', 'decor', 'tips'];
        $tags = collect($tagNames)->map(function($t){
            return Tag::firstOrCreate(['slug'=> Str::slug($t)], ['name'=>$t]);
        });

        // Posts
        $samples = [
            [
                'title' => '5 cách làm mới phòng khách tối giản',
                'excerpt' => 'Những bí quyết nhanh để làm mới không gian sống theo phong cách tối giản.',
                'content' => '<p>Gợi ý phối màu, ánh sáng và nội thất tối giản...</p>',
                'image' => null,
                'is_featured' => true,
            ],
            [
                'title' => 'Phong cách Scandinavian cho căn hộ nhỏ',
                'excerpt' => 'Ánh sáng tự nhiên, gỗ sáng màu và họa tiết đơn giản.',
                'content' => '<p>Các nguyên tắc cơ bản và gợi ý sản phẩm phù hợp...</p>',
                'image' => null,
                'is_featured' => true,
            ],
            [
                'title' => 'Mẹo tối ưu góc làm việc tại nhà',
                'excerpt' => 'Tối ưu công thái học, ánh sáng và sắp xếp gọn gàng.',
                'content' => '<p>Ghế công thái học, bàn phù hợp và phụ kiện cần thiết...</p>',
                'image' => null,
                'is_featured' => false,
            ],
        ];

        foreach ($samples as $i => $s) {
            $cat = $cats[$i % $cats->count()];
            $post = Post::updateOrCreate([
                'slug' => Str::slug($s['title'])
            ], [
                'title' => $s['title'],
                'excerpt' => $s['excerpt'],
                'content' => $s['content'],
                'status' => 'published',
                'published_at' => now()->subDays(10 - $i),
                'category_id' => $cat->id,
                'is_featured' => $s['is_featured'],
                'meta_title' => $s['title'].' | Meteor Shop',
                'meta_description' => $s['excerpt'],
            ]);
            // attach random tags
            $post->tags()->sync($tags->random(3)->pluck('id')->all());
        }
    }
}


