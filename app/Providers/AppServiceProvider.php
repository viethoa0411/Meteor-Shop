<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use App\Models\Category;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ✅ Chia sẻ danh mục con cho tất cả view (menu)
        try {
            if (DB::connection()->getPdo()) {
                View::share(
                    'childCategories',
                    Category::whereNotNull('parent_id')
                        ->where('status', 1)
                        ->get()
                );
            } else {
                View::share('childCategories', collect());
            }
        } catch (\Exception $e) {
            View::share('childCategories', collect());
        }
    }
}
