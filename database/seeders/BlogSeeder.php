<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $authorIds = User::pluck('id')->all();

        if (empty($authorIds)) {
            $this->call(AdminUserSeeder::class);
            $authorIds = User::pluck('id')->all();
        }

        $posts = [
            [
                'title' => '5 Gợi Ý Bố Trí Góc Làm Việc Tại Nhà',
                'excerpt' => 'Tối ưu công năng và thẩm mỹ cho góc làm việc của bạn.',
                'content' => 'Nội dung bài viết chi tiết về cách tối ưu ánh sáng, bàn ghế và phụ kiện...',
                'thumbnail' => 'blogs/workspace.jpg',
                'status' => 'published',
            ],
            [
                'title' => 'Chất Liệu Nội Thất Bền Vững',
                'excerpt' => 'Hướng dẫn lựa chọn vật liệu thân thiện môi trường.',
                'content' => 'Chi tiết về gỗ FSC, vải tái chế và quy trình sản xuất xanh...',
                'thumbnail' => 'blogs/materials.jpg',
                'status' => 'published',
            ],
            [
                'title' => 'Xu Hướng Trang Trí Phòng Khách 2025',
                'excerpt' => 'Gam màu trung tính và chất liệu tự nhiên lên ngôi.',
                'content' => 'Phân tích các xu hướng chủ đạo cùng gợi ý sản phẩm phù hợp...',
                'thumbnail' => 'blogs/living-room.jpg',
                'status' => 'published',
            ],
            [
                'title' => 'Checklist Chuẩn Bị Nhà Đón Tết',
                'excerpt' => 'Danh sách vật dụng giúp ngôi nhà tươi mới.',
                'content' => 'Các hạng mục nên dọn dẹp, thay mới nội thất, trang trí...',
                'thumbnail' => 'blogs/tet.jpg',
                'status' => 'draft',
            ],
            [
                'title' => 'Câu Chuyện Khách Hàng: Căn Hộ Aurora',
                'excerpt' => 'Hành trình cải tạo căn hộ 70m2 với phong cách Bắc Âu.',
                'content' => 'Phỏng vấn khách hàng, chia sẻ hình ảnh trước và sau khi thi công...',
                'thumbnail' => 'blogs/case-study.jpg',
                'status' => 'published',
            ],
        ];

        foreach ($posts as $index => $post) {
            Blog::updateOrCreate(
                ['slug' => Str::slug($post['title'])],
                array_merge($post, [
                    'slug' => Str::slug($post['title']),
                    'user_id' => $authorIds[$index % count($authorIds)],
                ])
            );
        }
    }
}
