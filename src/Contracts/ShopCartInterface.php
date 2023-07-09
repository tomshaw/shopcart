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
     * Get a cart item from collection by key or entire collection.
     *
     * @return \TomShaw\ShopCart\ShopCartItem|\Illuminate\Support\Collection
     */
    public function get(int $rowId = null): ShopCartItem|Collection|null;

    /**
     * Filter cart items by the given key value pair.
     *
     * @param  string  $key
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return \TomShaw\ShopCart\ShopCartItem
     */
    public function where($key, $operator, $value): null|Collection;

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
     * @param  int|null  $decimals
     * @param  null|string  $decimalSeperator
     * @param  null|string  $thousandsSeperator
     */
    public function total(string $property, $decimals = null, $decimalPoint = null, $thousandSeperator = null, $numberFormat = true): int|float|string;

    /**
     * Get collection items as JSON.
     */
    public function toJson(): string;

    /**
     * Get collection items as plain array.
     */
    public function toArray(): array;

    /**
     * Add a cart item to the collection.
     *
     * @param  \TomShaw\ShopCart\ShopCartItem
     */
    public function add(ShopCartItem $item): ShopCartItem;

    /**
     * Update an existing cart item.
     *
     * @param  \TomShaw\ShopCart\ShopCartItem
     */
    public function update(ShopCartItem $item): ShopCartItem;

    /**
     * Remove a cart item from the collection.
     *
     * @param  \TomShaw\ShopCart\ShopCartItem
     */
    public function remove(ShopCartItem $item): ShopCartItem;

    /**
     * Remove cart session.
     */
    public function forget(): void;
}
