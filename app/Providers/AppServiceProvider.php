<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Category;
use App\Models\Collection;

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
        View::share(
            'childCategories',
            Category::whereNotNull('parent_id')
                ->where('status', 1)
                ->get()
        );

        // ✅ Chia sẻ collections cho tất cả view (menu)
        View::share(
            'collections',
            Collection::active()
                ->orderBy('sort_order', 'asc')
                ->get()
        );
    }
}
