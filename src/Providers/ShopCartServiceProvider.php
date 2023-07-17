<?php

namespace TomShaw\ShopCart\Providers;

use Illuminate\Support\ServiceProvider;
use TomShaw\ShopCart\CartManager;

class ShopCartServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->bind('cart', CartManager::class);

        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../../config/cart.php' => config_path('shopcart.php')], 'config');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/cart.php', 'shopcart');
    }
}
