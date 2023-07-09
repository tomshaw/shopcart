<?php

namespace TomShaw\ShopCart\Facades;

use Illuminate\Support\Facades\Facade;
use TomShaw\ShopCart\ShopCart;

class ShopCartFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ShopCart::class;
    }
}
