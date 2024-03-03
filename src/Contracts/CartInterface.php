<?php

namespace TomShaw\ShopCart\Contracts;

use Illuminate\Support\Collection;
use TomShaw\ShopCart\CartItem;

interface CartInterface
{
    /**
     * Get all of the items in the collection.
     */
    public function all(bool $toArray = false): Collection|array;

    /**
     * Determine if an item exists in the collection by key.
     */
    public function has(int $rowId): bool;

    /**
     * Get cart item by key.
     */
    public function get(int $rowId): CartItem;

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
     * @param  string  $property  tax, price, subtotal and quantity
     */
    public function total(string $property, ?int $decimals = null, ?string $decimalSeperator = null, ?string $thousandsSeperator = null, bool $numberFormat = true): mixed;

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
     * Add cart item to collection.
     */
    public function add(CartItem $cartItem): CartItem;

    /**
     * Update cart item in collection.
     */
    public function update(CartItem $cartItem): CartItem;

    /**
     * Remove cart item from collection.
     */
    public function remove(CartItem $cartItem): CartItem;

    /**
     * Forget cart session.
     */
    public function forget(): void;
}
