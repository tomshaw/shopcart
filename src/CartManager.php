<?php

namespace TomShaw\ShopCart;

use Illuminate\Session\SessionManager;
use Illuminate\Support\Collection;
use TomShaw\ShopCart\Contracts\CartInterface;
use TomShaw\ShopCart\Events\ShopCartEvent;
use TomShaw\ShopCart\Exceptions\InvalidItemException;
use TomShaw\ShopCart\Helpers\Helpers;

final class CartManager implements CartInterface
{
    /**
     * The session storage key.
     */
    private string $sessionKey = 'shopcart.default';

    /**
     * Create a new cart instance.
     *
     * @return void
     */
    public function __construct(
        private SessionManager $session
    ) {
    }

    /**
     * Get all of the items in the collection.
     */
    protected function cart(): Collection
    {
        return $this->session->get($this->sessionKey) ?? new Collection();
    }

    /**
     * Persists cart collection.
     */
    protected function persist(Collection $collection): void
    {
        $this->session->put($this->sessionKey, $collection);
    }

    /**
     * Get all of the items in the collection.
     */
    public function all(bool $toArray = false): Collection|array
    {
        return $toArray ? $this->cart()->all() : $this->cart();
    }

    /**
     * Determine if an item exists in the collection by key.
     */
    public function has(int $rowId): bool
    {
        return $this->cart()->has($rowId);
    }

    /**
     * Get cart item by key.
     */
    public function get(int $rowId): CartItem
    {
        return $this->cart()->get($rowId);
    }

    /**
     * Filter cart items by the given key value pair.
     *
     * @param  string  $key
     * @param  mixed  $operator
     * @param  mixed  $value
     */
    public function where($key, $operator, $value): Collection
    {
        return $this->cart()->where($key, $operator, $value);
    }

    /**
     * Determine if the cart collection is not empty.
     */
    public function isNotEmpty(): bool
    {
        return $this->cart()->isNotEmpty();
    }

    /**
     * Determine if the cart collection is empty.
     */
    public function isEmpty(): bool
    {
        return $this->cart()->isEmpty();
    }

    /**
     * Determine cart totals supports "tax", "price", "subtotal" and "quantity".
     *
     * @param  string  $property  tax, price, subtotal and quantity
     */
    public function total(string $property, ?int $decimals = null, ?string $decimalSeperator = null, ?string $thousandsSeperator = null, bool $numberFormat = true): mixed
    {
        $found = match ($property) {
            'tax' => 'totalTax',
            'price' => 'totalPrice',
            'subtotal' => 'subTotal',
            'quantity' => 'quantity',
            default => 'totalPrice'
        };

        $result = $this->cart()->sum($found);

        return ($numberFormat) ? Helpers::numberFormat($result, $decimals, $decimalSeperator, $thousandsSeperator) : $result;
    }

    /**
     * Get collection items as JSON.
     */
    public function toJson(): string
    {
        return $this->cart()->toJson();
    }

    /**
     * Get collection items as plain array.
     *
     * return array
     */
    public function toArray(): array
    {
        return json_decode(json_encode($this->all()), true);
    }

    /**
     * Add cart item to collection.
     */
    public function add(CartItem $cartItem): CartItem
    {
        if (! $cartItem->tax) {
            $cartItem->tax = floatval(config('shopcart.default.tax'));
        }

        $cartItem->process();

        $collection = $this->cart()->put($cartItem->rowId, $cartItem);

        $this->persist($collection);

        event(new ShopCartEvent(method: 'add', cartItem: $cartItem));

        return $cartItem;
    }

    /**
     * Update cart item in collection.
     */
    public function update(CartItem $cartItem): CartItem
    {
        CartItem::validate($cartItem->id, $cartItem->name, $cartItem->quantity, $cartItem->price, $cartItem->tax);

        $cartItem->process();

        $collection = $this->cart()->put($cartItem->rowId, $cartItem);

        $this->persist($collection);

        event(new ShopCartEvent(method: 'update', cartItem: $cartItem));

        return $cartItem;
    }

    /**
     * Remove cart item from collection.
     */
    public function remove(CartItem $cartItem): CartItem
    {
        if (! $this->has($cartItem->rowId)) {
            throw new InvalidItemException('Cart item not found.');
        }

        $collection = $this->cart()->forget($cartItem->rowId);

        $this->persist($collection);

        event(new ShopCartEvent(method: 'remove', cartItem: $cartItem));

        return $cartItem;
    }

    /**
     * Forget cart session.
     */
    public function forget(): void
    {
        $this->session->forget($this->sessionKey);

        event(new ShopCartEvent(method: 'forget', cartItem: null));
    }
}
