<?php

namespace TomShaw\ShopCart\Events;

use TomShaw\ShopCart\ShopCartItem;

class ShopCartEvent
{
    public function __construct(
        public string $method,
        public ShopCartItem|null $cartItem
    ) {
    }
}
