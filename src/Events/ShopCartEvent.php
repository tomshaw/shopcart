<?php

namespace TomShaw\ShopCart\Events;

use TomShaw\ShopCart\CartItem;

class ShopCartEvent
{
    public function __construct(
        public string $method,
        public ?CartItem $cartItem
    ) {
    }
}
