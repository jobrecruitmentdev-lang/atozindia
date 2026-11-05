<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        View::composer(['layouts.app', 'shop.*'], function ($view) {
            $megaCategories = Cache::remember('global_categories_tree', 86400, function () {
                return Category::whereNull('parent_id')
                    ->where('is_active', true)
                    ->with(['children' => function ($q) {
                        $q->where('is_active', true)->orderBy('sort_order');
                    }])
                    ->orderBy('sort_order')
                    ->get();
            });
            $view->with('megaCategories', $megaCategories);
        });
    }
}
