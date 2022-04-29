<?php

namespace App\Providers;

use App\Repository\Category\CategoryRepository;
use App\Repository\Category\CategoryRepositoryInterface;
use App\Repository\Product\ProductRepository;
use App\Repository\Product\ProductRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(ProductRepositoryInterface::class,ProductRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class,CategoryRepository::class);
    }
}
