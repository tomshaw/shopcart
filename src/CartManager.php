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
    private string $sessionKey = 'shopcart.default';

    public function __construct(
        private SessionManager $session
    ) {}

    protected function cart(): Collection
    {
        return $this->session->get($this->sessionKey) ?? new Collection;
    }

    protected function persist(Collection $collection): void
    {
        $this->session->put($this->sessionKey, $collection);
    }

    public function all(bool $toArray = false): Collection|array
    {
        return $toArray ? $this->cart()->all() : $this->cart();
    }

    public function has(int $rowId): bool
    {
        return $this->cart()->has($rowId);
    }

    public function get(int $rowId): CartItem
    {
        return $this->cart()->get($rowId);
    }

    public function where($key, $operator, $value): Collection
    {
        return $this->cart()->where($key, $operator, $value);
    }

    public function isNotEmpty(): bool
    {
        return $this->cart()->isNotEmpty();
    }

    public function isEmpty(): bool
    {
        return $this->cart()->isEmpty();
    }

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

    public function toJson(): string
    {
        return $this->cart()->toJson();
    }

    public function toArray(): array
    {
        return json_decode(json_encode($this->all()), true);
    }

    public function add(CartItem $cartItem): CartItem
    {
        if (! $cartItem->tax) {
            $cartItem->tax = floatval(config('shopcart.default.tax'));
        }

        $collection = $this->cart()->put($cartItem->rowId, $cartItem);

        $this->persist($collection);

        event(new ShopCartEvent(method: 'add', cartItem: $cartItem));

        return $cartItem;
    }

    public function update(CartItem $cartItem): CartItem
    {
        CartItem::validate($cartItem->id, $cartItem->name, $cartItem->quantity, $cartItem->price, $cartItem->tax);

        $collection = $this->cart()->put($cartItem->rowId, $cartItem);

        $this->persist($collection);

        event(new ShopCartEvent(method: 'update', cartItem: $cartItem));

        return $cartItem;
    }

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

    public function forget(): void
    {
        $this->session->forget($this->sessionKey);

        event(new ShopCartEvent(method: 'forget', cartItem: null));
    }
}
