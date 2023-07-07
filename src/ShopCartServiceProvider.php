<?php

namespace TomShaw\ShopCart;

use Illuminate\Support\ServiceProvider;

class ShopCartServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->bind('shopcart', ShopCart::class);

        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../config/cart.php' => config_path('shopcart.php')], 'config');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/cart.php', 'shopcart');
    }
}