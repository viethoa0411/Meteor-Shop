<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    // // hiển thị sản phẩm theo danh mục 
   public function productsByCategory($slug)
    {
        $cate = Category::all();
        $category = Category::where('slug', $slug)->firstOrFail();
        $childIds = Category::where('parent_id', $category->id)->pluck('id');
        if ($childIds->count() > 0) {
            $products = Product::whereIn('category_id', $childIds)
                ->orderBy('created_at', 'desc')
                ->paginate(12);

        } else {
            $products = Product::where('category_id', $category->id)
                ->orderBy('created_at', 'desc')
                ->paginate(12);
        }
        return view('client.products.category', compact('category', 'products', 'cate'));
    }

     public function showDetail($slug)
    {
        $cate = Category::all(); // danh mục: tất cả các danh mục
        $product = Product::with('images')->where('slug', $slug)->firstOrFail(); 
        $relatedProducts = Product::where('category_id', $product->category_id) // lấy sản phẩm khác thuộc cùng danh mục(trùng category_id)
                            ->where('id', '!=', $product->id) // loại bỏ sản phẩm đang xem theo id
                            ->take(4) // lấy 4sp
                            ->get(); // hiển thị 

        return view('client.products.detail', compact('product', 'relatedProducts', 'cate'));
    }
      
     public function index()
    {
        $categories = Category::take(6)->get(); // Lấy 6 danh mục cha
        foreach ($categories as $category) {
        $childIds = Category::where('parent_id', $category->id)->pluck('id'); // Với mỗi danh mục cha, lấy các ID danh mục con
        $category->latestProducts = Product::whereIn('category_id', $childIds)
   ->orderBy('created_at', 'desc')
            ->take(4)
            ->get(); //Thực thi truy vấn, trả về danh sách 4 sản phẩm đó.
        }
        return view('client.products.index', compact('categories'));
    }
     // Trang hiển thị sản phẩm theo 1 danh mục cụ thể
    
     public function listProductsByCategory($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();  // tìm 1 danh mục dựa trên slug(đường dẫn, nếu không có danh mục nào, laravel tự trả về 404)
        $products = Product::where('category_id', $category->id) //hiển thị tất cả sản phẩm ở danh mục trên 
            ->orderBy('created_at', 'desc')  
            ->paginate(12); // chia kết quả thành 12 sản phẩm mỗi trang, Laravel tự động tạo nút phân trang ({{ $products->links() }}).

        return view('client.products.category', compact('category', 'products'));
    }
}