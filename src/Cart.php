<?php

namespace TomShaw\ShopCart;

use Illuminate\Support\Facades\Facade;

class Cart extends Facade
{
    protected static function getFacadeAccessor()
    {
        return CartManager::class;
    }
}
