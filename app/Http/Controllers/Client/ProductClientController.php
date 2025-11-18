<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductClientController extends Controller
{
    public function productsByCategory($slug)
    {
        Category::where('slug', $slug)->firstOrFail();

        return redirect()->route('client.product.search', ['category' => $slug]);
    }
    public function showDetail($slug)
    {
        $cate = Category::all();

        // Lấy sản phẩm + ảnh phụ
        $product = Product::with('images')->where('slug', $slug)->firstOrFail();

        // Lấy sản phẩm liên quan
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('client.products.detail', compact('product', 'relatedProducts', 'cate'));
    }

    /**
     * Lấy danh sách ID của danh mục và toàn bộ danh mục con (đệ quy/BFS).
     *
     * @param int $rootCategoryId ID của danh mục gốc
     * @return array Mảng chứa ID của danh mục gốc và tất cả danh mục con
     */
    public function search(Request $request, ?string $slug = null)
    {
        $searchQuery = trim($request->input('query'));
        $sort = $request->input('sort', 'newest');
        $categoryInput = $request->input('category');
        $minPrice = $request->input('minPrice');
        $maxPrice = $request->input('maxPrice');

        // Lấy danh mục đang hoạt động (cần thiết để truyền sang view cho menu/filter)
        $cate = Category::query()
            ->select(['id', 'name', 'slug', 'description', 'parent_id', 'status'])
            ->where('status', 1)
            ->get();

        // Khởi tạo truy vấn sản phẩm
        $query = Product::query()
            ->select(['id', 'name', 'slug', 'price', 'image', 'status', 'description', 'created_at', 'category_id'])
            ->where('status', 1);

        // ✅ Lọc theo danh mục nếu có
        $selectedCategory = null;
        if ($slug) {
            $selectedCategory = Category::where('slug', $slug)->firstOrFail();
        } elseif ($categoryInput) {
            $selectedCategory = is_numeric($categoryInput)
                ? Category::find($categoryInput)
                : Category::where('slug', $categoryInput)->first();
            if (!$selectedCategory && is_numeric($categoryInput)) {
                abort(404);
            }
        }

        if ($selectedCategory) {
            $categoryIds = $this->getDescendantCategoryIds((int) $selectedCategory->id);
            $query->whereIn('category_id', $categoryIds);
        }

        // ✅ Lọc theo từ khóa nếu người dùng nhập
        if ($searchQuery) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('name', 'LIKE', "%{$searchQuery}%")
                    ->orWhere('description', 'LIKE', "%{$searchQuery}%")
                    ->orWhere('slug', 'LIKE', "%{$searchQuery}%");
            });
        }

        // ✅ Lọc theo giá slider
        if ($minPrice && $maxPrice) {
            $query->whereBetween('price', [$minPrice, $maxPrice]);
        } elseif ($minPrice) {
            $query->where('price', '>=', $minPrice);
        } elseif ($maxPrice) {
            $query->where('price', '<=', $maxPrice);
        }

        // ✅ Sắp xếp
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

        // ✅ Phân trang
        $products = $query->paginate(8)->withQueryString();

        return view('client.search', compact('products', 'searchQuery', 'cate', 'selectedCategory'));
    }

    private function getDescendantCategoryIds(int $rootCategoryId)
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
