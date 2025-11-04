<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
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
        // Share categories cho tất cả views client
        View::composer('client.layout', function ($view) {
            $categories = Category::query()
                ->where('status', 'active')
                ->with('parent:id,name,slug')
                ->get();
            $view->with('categories', $categories);
        });
    }
}
