<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Trang danh sách sản phẩm theo "Phòng"
     * - Lọc theo phòng (danh mục cha)
     * - Lọc theo khoảng giá
     * - Sắp xếp
     */
    public function index(Request $request)
    {
        // Danh mục để truyền cho layout (menu)
        $cate = Category::query()
            ->select(['id', 'name', 'slug', 'description', 'parent_id', 'status'])
            ->where('status', 1)
            ->get();

        // Các danh mục cha được xem là "Phòng"
        $rooms = $cate->whereNull('parent_id');

        $roomInput = $request->input('room'); // id hoặc slug
        $sort = $request->input('sort', 'newest');
        $minPrice = $request->input('minPrice');
        $maxPrice = $request->input('maxPrice');

        $selectedRoom = null;
        if ($roomInput) {
            $selectedRoom = is_numeric($roomInput)
                ? Category::whereNull('parent_id')->where('id', $roomInput)->first()
                : Category::whereNull('parent_id')->where('slug', $roomInput)->first();
        }

        // Query sản phẩm
        $query = Product::query()
            ->select(['id', 'name', 'slug', 'price', 'image', 'status', 'description', 'created_at', 'category_id'])
            ->where('status', 1);

        if ($selectedRoom) {
            $categoryIds = $this->getDescendantCategoryIds((int) $selectedRoom->id);
            $query->whereIn('category_id', $categoryIds);
        }

        // Lọc theo giá
        if ($minPrice && $maxPrice) {
            $query->whereBetween('price', [$minPrice, $maxPrice]);
        } elseif ($minPrice) {
            $query->where('price', '>=', $minPrice);
        } elseif ($maxPrice) {
            $query->where('price', '<=', $maxPrice);
        }

        // Sắp xếp
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        $title = 'Sản phẩm theo phòng';

        return view('client.rooms.index', compact(
            'products',
            'rooms',
            'selectedRoom',
            'cate',
            'title',
            'minPrice',
            'maxPrice',
            'sort'
        ));
    }

    /**
     * Lấy toàn bộ id danh mục con (bao gồm chính nó)
     */
    protected function getDescendantCategoryIds(int $rootCategoryId): array
    {
        $allIds = [$rootCategoryId];
        $queue = [$rootCategoryId];

        while (!empty($queue)) {
            $currentId = array_shift($queue);
            $children = Category::where('parent_id', $currentId)->pluck('id')->all();

            foreach ($children as $childId) {
                if (!in_array($childId, $allIds, true)) {
                    $allIds[] = $childId;
                    $queue[] = $childId;
                }
            }
        }

        return $allIds;
    }
}


