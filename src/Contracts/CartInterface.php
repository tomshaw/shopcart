<?php

namespace TomShaw\ShopCart\Contracts;

use Illuminate\Support\Collection;
use TomShaw\ShopCart\CartItem;

interface CartInterface
{
    public function all(bool $toArray = false): Collection|array;

    public function has(int $rowId): bool;

    public function get(int $rowId): CartItem;

    public function total(string $property, ?int $decimals = null, ?string $decimalSeperator = null, ?string $thousandsSeperator = null, bool $numberFormat = true): mixed;

    public function toJson(): string;

    public function toArray(): array;

    public function add(CartItem $cartItem): CartItem;

    public function update(CartItem $cartItem): CartItem;

    public function remove(CartItem $cartItem): CartItem;

    public function forget(): void;
}
