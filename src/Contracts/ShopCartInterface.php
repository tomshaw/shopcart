<?php

namespace TomShaw\ShopCart\Contracts;

use Illuminate\Support\Collection;
use TomShaw\ShopCart\ShopCartItem;

interface ShopCartInterface
{
    /**
     * Get all of the items in the collection.
     */
    public function all(): array;

    /**
     * Determine if an item exists in the collection by key.
     */
    public function has(int $rowId): bool;

    /**
     * Get a cart item by key.
     *
     * @return \TomShaw\ShopCart\ShopCartItem|\Illuminate\Support\Collection
     */
    public function get(int $rowId = null): mixed;

    /**
     * Filter cart items by the given key value pair.
     *
     * @param  string  $key
     * @param  mixed  $operator
     * @param  mixed  $value
     */
    public function where($key, $operator, $value): Collection;

    /**
     * Determine if the cart collection is not empty.
     */
    public function isNotEmpty(): bool;

    /**
     * Determine if the cart collection is empty.
     */
    public function isEmpty(): bool;

    /**
     * Determine cart totals supports "tax", "price", "subtotal" and "quantity".
     *
     * @param  string  $property tax, price, subtotal and quantity
     * @param  int  $decimals
     * @param  string  $decimalSeperator
     * @param  string  $thousandsSeperator
     */
    public function total(string $property, int $decimals = null, string $decimalSeperator = null, string $thousandsSeperator = null, bool $numberFormat = true): mixed;

    /**
     * Get collection items as JSON.
     */
    public function toJson(): string;

    /**
     * Get collection items as plain array.
     *
     * return array
     */
    public function toArray(): array;

    /**
     * Add a cart item to the collection.
     */
    public function add(ShopCartItem $cartItem): ShopCartItem;

    /**
     * Update an existing cart item.
     */
    public function update(ShopCartItem $cartItem): ShopCartItem;

    /**
     * Remove a cart item from the collection.
     */
    public function remove(ShopCartItem $cartItem): ShopCartItem;

    /**
     * Remove cart session.
     */
    public function forget(): void;
}
