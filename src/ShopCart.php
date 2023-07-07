<?php

namespace TomShaw\ShopCart;

use Illuminate\Session\SessionManager;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Collection;

use TomShaw\ShopCart\Contracts\ShopCartInterface;
use TomShaw\ShopCart\Exceptions\InvalidItemException;
use TomShaw\ShopCart\Helpers\Helpers;

final class ShopCart implements ShopCartInterface
{
    /**
     * The session storage key.
     *
     * @var string
     */
    private string $sessionKey = 'shopcart.default';

    /**
     * Create a new cart instance.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @param  \Illuminate\Session\SessionManager  $session
     * @return void
     */
    function __construct(
        private Dispatcher $events,
        private SessionManager $session
    ) {
    }

    /**
     * Retrieve the cart collection.
     * 
     * @return \Illuminate\Support\Collection
     */
    protected function cart(): Collection
    {
        return $this->session->get($this->sessionKey) ?? new Collection;
    }

    /**
     * Persists the cart collection.
     * 
     * @return void
     */
    protected function persist(Collection $collection): void
    {
        $this->session->put($this->sessionKey, $collection);
    }

    /**
     * Get all of the items in the collection.
     * 
     * @return array
     */
    public function all(): array
    {
        return $this->cart()->all();
    }

    /**
     * Determine if an item exists in the collection by key.
     * 
     * @param  int  $rowId
     * @return bool
     */
    public function has(int $rowId): bool
    {
        return $this->cart()->has($rowId);
    }

    /**
     * Get a cart item from collection by key or entire collection.
     * 
     * @param  int|null  $rowId
     * @return \TomShaw\ShopCart\ShopCartItem|\Illuminate\Support\Collection
     */
    public function get(int $rowId = null): ShopCartItem | Collection | null
    {
        if ($rowId) {
            return $this->cart()->get($rowId);
        }

        return $this->cart();
    }

    /**
     * Filter cart items by the given key value pair.
     * 
     * @param  string  $key
     * @param  mixed  $operator
     * @param  mixed  $value
     * @return \TomShaw\ShopCart\ShopCartItem
     */
    public function where($key, $operator, $value): null | Collection
    {
        return $this->cart()->where($key, $operator, $value);
    }

    /**
     * Determine if the cart collection is not empty.
     * 
     * @return bool
     */
    public function isNotEmpty(): bool
    {
        return $this->cart()->isNotEmpty();
    }

    /**
     * Determine if the cart collection is empty.
     * 
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->cart()->isEmpty();
    }

    /**
     * Determine cart totals supports "tax", "price", "subtotal" and "quantity".
     * 
     * @param  string  $property tax, price, subtotal and quantity
     * @param  int|null  $decimals
     * @param  null|string  $decimalSeperator
     * @param  null|string  $thousandsSeperator
     * @return int|float|string
     */
    public function total(string $property, $decimals = null, $decimalPoint = null, $thousandSeperator = null, $numberFormat = true): int | float | string
    {
        $found = match ($property) {
            'tax' => 'totalTax',
            'price' => 'totalPrice',
            'subtotal' => 'subTotal',
            'quantity' => 'quantity',
            default => 'totalPrice'
        };

        $result = $this->cart()->sum($found);

        return ($numberFormat) ? Helpers::numberFormat($result, $decimals, $decimalPoint, $thousandSeperator) : $result;
    }

    /**
     * Get collection items as JSON.
     * 
     * @return string
     */
    public function toJson(): string
    {
        return $this->cart()->toJson();
    }

    /**
     * Get collection items as plain array.
     * 
     * @return array
     */
    public function toArray(): array
    {
        return json_decode(json_encode($this->all()), true);
    }

    /**
     * Add a cart item to the collection.
     * 
     * @param  \TomShaw\ShopCart\ShopCartItem
     * @return \TomShaw\ShopCart\ShopCartItem
     */
    public function add(ShopCartItem $cartItem): ShopCartItem
    {
        if (!$cartItem->tax) {
            $cartItem->tax = floatval(config('shopcart.default.tax'));
        }

        $cartItem->process();

        $collection = $this->cart()->put($cartItem->rowId, $cartItem);

        $this->persist($collection);

        $this->events->dispatch('shopcart.add', $cartItem->rowId);

        return $cartItem;
    }

    /**
     * Update an existing cart item.
     * 
     * @param  \TomShaw\ShopCart\ShopCartItem
     * @return \TomShaw\ShopCart\ShopCartItem
     */
    public function update(ShopCartItem $cartItem): ShopCartItem
    {
        ShopCartItem::validate($cartItem->id, $cartItem->name, $cartItem->quantity, $cartItem->price, $cartItem->tax);

        $cartItem->process();

        $collection = $this->cart()->put($cartItem->rowId, $cartItem);

        $this->persist($collection);

        $this->events->dispatch('shopcart.update', $cartItem->rowId);

        return $cartItem;
    }

    /**
     * Remove a cart item from the collection.
     * 
     * @param  \TomShaw\ShopCart\ShopCartItem
     * @return \TomShaw\ShopCart\ShopCartItem
     */
    public function remove(ShopCartItem $cartItem): ShopCartItem
    {
        if (!$this->has($cartItem->rowId)) {
            throw new InvalidItemException("Cart item not found.");
        }

        $collection = $this->cart()->forget($cartItem->rowId);

        $this->persist($collection);

        $this->events->dispatch('shopcart.remove', $cartItem->rowId);

        return $cartItem;
    }

    /**
     * Remove cart session.
     * 
     * @return void
     */
    public function forget(): void
    {
        $this->session->forget($this->sessionKey);

        $this->events->dispatch('shopcart.empty');
    }
}
