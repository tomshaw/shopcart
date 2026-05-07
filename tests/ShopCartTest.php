<?php

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use TomShaw\ShopCart\Cart;
use TomShaw\ShopCart\CartItem;
use TomShaw\ShopCart\Events\ShopCartEvent;
use TomShaw\ShopCart\Exceptions\InvalidItemException;
use TomShaw\ShopCart\Helpers\Helpers;

beforeEach(function () {
    $this->cartItemA = Cart::add(CartItem::make(id: 1, name: 'Socks', quantity: 12, price: 10.00));
    $this->cartItemB = Cart::add(CartItem::make(id: 2, name: 'Shoes', quantity: 2, price: 119.95, tax: 8.25));
    $this->cartItemC = Cart::add(CartItem::make(id: 3, name: 'Pants', quantity: 3, price: 30.00, tax: 7.25));
    $this->cartItemD = Cart::add(CartItem::make(id: 4, name: 'Shirts', quantity: 5, price: 40.00, tax: 5.30));
    $this->cartItemE = Cart::add(CartItem::make(id: 5, name: 'Shades', quantity: 1, price: 99.95, tax: 6.25));
});

test('cart should throw exception when provided invalid name', function () {
    Cart::add(CartItem::make(1, '', 1, 1.00));
})->throws(InvalidItemException::class);

test('cart should throw exception when provided invalid quantity', function () {
    Cart::add(CartItem::make(1, 'Test Item', 0, 1.00));
})->throws(InvalidItemException::class);

test('cart item validate throws on missing id', function () {
    CartItem::validate(null, 'Test', 1, 1.00);
})->throws(InvalidItemException::class);

test('cart item validate throws on missing price', function () {
    CartItem::validate(1, 'Test', 1, null);
})->throws(InvalidItemException::class);

test('cart update revalidates the item', function () {
    $item = Cart::all()->first();
    $item->quantity = 0;

    Cart::update($item);
})->throws(InvalidItemException::class);

test('cart get method returns correct data', function () {
    expect(Cart::get($this->cartItemA->rowId)->id)->toEqual($this->cartItemA->id);
    expect(Cart::get($this->cartItemB->rowId)->name)->toEqual($this->cartItemB->name);
    expect(Cart::get($this->cartItemC->rowId)->quantity)->toEqual($this->cartItemC->quantity);
    expect(Cart::get($this->cartItemC->rowId)->price)->toEqual($this->cartItemC->price);
});

test('cart has method returns correct boolean value', function () {
    $cartItem = Cart::all()->random();

    expect(Cart::has($cartItem->rowId))->toBeTrue();
    expect(Cart::has($cartItem->rowId - 1))->toBeFalse();
});

test('cart should delete correct item', function () {
    $cartItem = Cart::all()->random();

    Cart::remove($cartItem);

    expect(Cart::has($cartItem->rowId))->toBeFalse();
});

test('cart remove throws when item is not in cart', function () {
    $orphan = new CartItem(id: 99, name: 'Orphan', quantity: 1, price: 1.00);

    Cart::remove($orphan);
})->throws(InvalidItemException::class, 'Cart item not found.');

test('cart should empty', function () {
    Cart::forget();

    expect(Cart::all()->isEmpty())->toBeTrue();
});

test('cart should update and return correct cart totals', function () {
    $cartItem = Cart::all()->where('id', '===', 2)->first();

    $cartItem->quantity = 3;
    Cart::update($cartItem);

    expect(Cart::total('price'))->toEqual(922.86);
    expect(Cart::total('quantity', numberFormat: false))->toEqual(24);

    $cartItem->price = 15.00;
    Cart::update($cartItem);

    expect(Cart::total('price'))->toEqual(582.03);

    $cartItem->quantity = 5;
    Cart::update($cartItem);

    expect(Cart::total('price'))->toEqual(614.51);
    expect(Cart::total('quantity', numberFormat: false))->toEqual(26);
});

test('cart total tax sums totalTax across items', function () {
    expect(Cart::total('tax'))->toEqual('43.16');
});

test('cart total subtotal sums subTotal across items', function () {
    expect(Cart::total('subtotal'))->toEqual('749.85');
});

test('cart total falls back to totalPrice for unknown property', function () {
    expect(Cart::total('unknown'))->toEqual(Cart::total('price'));
});

test('cart total honors custom decimals and separators', function () {
    Cart::forget();
    Cart::add(CartItem::make(id: 1, name: 'Test', quantity: 1, price: 1234.5678));

    expect(Cart::total('price', decimals: 2, decimalSeperator: ',', thousandsSeperator: '.'))
        ->toEqual('1.234,57');
});

test('cart should apply item-specific tax rate', function () {
    $cartItemA = Cart::add(CartItem::make(1, 'Socks', 1, 10.00));
    $cartItemB = Cart::add(CartItem::make(1, 'Socks', 1, 10.00, 6.25));

    expect($cartItemA->tax)->toEqual(0);
    expect($cartItemB->tax)->toEqual(6.25);
});

test('cart should return calculated tax rate', function () {
    $cartItemA = Cart::add(CartItem::make(1, 'Socks', 1, 10.00));
    $cartItemB = Cart::add(CartItem::make(1, 'Socks', 1, 10.00, 6.25));

    expect($cartItemA->getCalculatedTaxRate(precision: 2))->toEqual(0);
    expect($cartItemB->getCalculatedTaxRate(precision: 2))->toEqual(6.25);
});

test('cart applies default tax from config when item tax is null', function () {
    config()->set('shopcart.default.tax', 5.5);

    $item = Cart::add(CartItem::make(id: 100, name: 'Hat', quantity: 1, price: 20.00));

    expect($item->tax)->toEqual(5.5);
});

test('cart preserves item-specific tax over config default', function () {
    config()->set('shopcart.default.tax', 5.5);

    $item = Cart::add(CartItem::make(id: 101, name: 'Cap', quantity: 1, price: 20.00, tax: 8.25));

    expect($item->tax)->toEqual(8.25);
});

test('cart all returns collection by default', function () {
    expect(Cart::all())->toBeInstanceOf(Collection::class);
});

test('cart all returns array when toArray is true', function () {
    expect(Cart::all(toArray: true))->toBeArray()->toHaveCount(5);
});

test('cart toJson returns valid JSON of all items', function () {
    $json = Cart::toJson();

    expect($json)->toBeString();
    expect(json_decode($json, true))->toBeArray()->toHaveCount(5);
});

test('cart toArray returns array of associative item arrays', function () {
    $array = Cart::toArray();

    expect($array)->toBeArray()->toHaveCount(5);

    $first = array_values($array)[0];
    expect($first)->toHaveKeys(['id', 'name', 'quantity', 'price']);
});

test('cart should dispatch shopcart events', function () {
    Event::fake();

    $cartItem = Cart::add(CartItem::make(10, 'Socks', 1, 10.00));

    Event::assertDispatched(ShopCartEvent::class, fn ($event) => $event->method === 'add');
    Event::assertDispatched(ShopCartEvent::class, fn ($event) => $event->cartItem->id === $cartItem->id);

    Cart::update($cartItem);

    Event::assertDispatched(ShopCartEvent::class, fn ($event) => $event->method === 'update');

    Cart::remove($cartItem);

    Event::assertDispatched(ShopCartEvent::class, fn ($event) => $event->method === 'remove');

    Cart::forget();

    Event::assertDispatched(
        ShopCartEvent::class,
        fn ($event) => $event->method === 'forget' && $event->cartItem === null
    );
});

test('cart item subTotal equals quantity times price', function () {
    $item = new CartItem(id: 1, name: 'Test', quantity: 4, price: 25.00);

    expect($item->subTotal)->toEqual(100.00);
});

test('cart item totalTax equals subTotal times tax percentage', function () {
    $item = new CartItem(id: 1, name: 'Test', quantity: 4, price: 25.00, tax: 10.0);

    expect($item->totalTax)->toEqual(10.00);
});

test('cart item totalPrice equals subTotal plus totalTax', function () {
    $item = new CartItem(id: 1, name: 'Test', quantity: 4, price: 25.00, tax: 10.0);

    expect($item->totalPrice)->toEqual(110.00);
});

test('cart item totalTax is zero when tax is null', function () {
    $item = new CartItem(id: 1, name: 'Test', quantity: 4, price: 25.00);

    expect($item->totalTax)->toEqual(0.0);
});

test('cart item options default to empty collection', function () {
    $item = new CartItem(id: 1, name: 'Test', quantity: 1, price: 1.00);

    expect($item->options)->toBeInstanceOf(Collection::class);
    expect($item->options->isEmpty())->toBeTrue();
});

test('cart item accepts options collection', function () {
    $options = collect(['size' => 'L', 'color' => 'red']);
    $item = new CartItem(id: 1, name: 'Tee', quantity: 1, price: 20.00, options: $options);

    expect($item->options)->toBeInstanceOf(Collection::class);
    expect($item->options->get('size'))->toEqual('L');
    expect($item->options->get('color'))->toEqual('red');
});

test('validate item throws on unknown property name', function () {
    CartItem::validateItem('unknown', 'foo');
})->throws(InvalidItemException::class, 'Invalid property name.');

test('validate item throws on invalid value for known property', function () {
    CartItem::validateItem('quantity', 0);
})->throws(InvalidItemException::class);

test('validate item passes for valid value', function () {
    $result = CartItem::validateItem('name', 'Some Name');

    expect($result)->not->toBeNull();
});

test('helpers number format applies decimals and separators', function () {
    expect(Helpers::numberFormat(1234567.891, 2, '.', ','))->toEqual('1,234,567.89');
});

test('helpers number format falls back to config when arguments are null', function () {
    expect(Helpers::numberFormat(1234.5))->toEqual('1,234.50');
});

test('helpers round rounds half up by default', function () {
    expect(Helpers::round(1.5))->toEqual(2.0);
    expect(Helpers::round(2.5))->toEqual(3.0);
});

test('helpers round honors PHP_ROUND_HALF_DOWN mode', function () {
    expect(Helpers::round(1.5, 0, PHP_ROUND_HALF_DOWN))->toEqual(1.0);
    expect(Helpers::round(2.5, 0, PHP_ROUND_HALF_DOWN))->toEqual(2.0);
});

test('helpers round honors precision argument', function () {
    expect(Helpers::round(3.14159, 2))->toEqual(3.14);
});
