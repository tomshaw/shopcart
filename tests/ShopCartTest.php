<?php

namespace TomShaw\ShopCart\Tests;

use Illuminate\Support\Facades\Event;
use TomShaw\ShopCart\Cart;
use TomShaw\ShopCart\CartItem;
use TomShaw\ShopCart\Events\ShopCartEvent;
use TomShaw\ShopCart\Exceptions\InvalidItemException;

class ShopCartTest extends TestCase
{
    protected CartItem $cartItemA;

    protected CartItem $cartItemB;

    protected CartItem $cartItemC;

    protected CartItem $cartItemD;

    protected CartItem $cartItemE;

    public function setup(): void
    {
        parent::setUp();

        $this->cartItemA = Cart::add(CartItem::make(id: 1, name: 'Socks', quantity: 12, price: 10.00));
        $this->cartItemB = Cart::add(CartItem::make(id: 2, name: 'Shoes', quantity: 2, price: 119.95, tax: 8.25));
        $this->cartItemC = Cart::add(CartItem::make(id: 3, name: 'Pants', quantity: 3, price: 30.00, tax: 7.25));
        $this->cartItemD = Cart::add(CartItem::make(id: 4, name: 'Shirts', quantity: 5, price: 40.00, tax: 5.30));
        $this->cartItemE = Cart::add(CartItem::make(id: 5, name: 'Shades', quantity: 1, price: 99.95, tax: 6.25));
    }

    public function test_cart_should_throw_exception_when_provided_invalid_name()
    {
        $this->expectException(InvalidItemException::class);

        Cart::add(CartItem::make(1, '', 1, 1.00));
    }

    public function test_cart_should_throw_exception_when_provided_invalid_quantity()
    {
        $this->expectException(InvalidItemException::class);

        Cart::add(CartItem::make(1, 'Test Item', 0, 1.00));
    }

    public function test_cart_get_method_returns_correct_data(): void
    {
        $this->assertEquals(Cart::get($this->cartItemA->rowId)->id, $this->cartItemA->id);
        $this->assertEquals(Cart::get($this->cartItemB->rowId)->name, $this->cartItemB->name);
        $this->assertEquals(Cart::get($this->cartItemC->rowId)->quantity, $this->cartItemC->quantity);
        $this->assertEquals(Cart::get($this->cartItemC->rowId)->price, $this->cartItemC->price);
    }

    public function test_cart_has_method_returns_correct_boolean_value(): void
    {
        $cartItem = Cart::all()->random();

        $this->assertTrue(Cart::has($cartItem->rowId));
        $this->assertFalse(Cart::has($cartItem->rowId - 1));
    }

    public function test_cart_should_update_and_return_correct_cart_totals(): void
    {
        $cartItem = Cart::where('id', '===', 2)->first();

        $cartItem->quantity = 3;

        Cart::update($cartItem);

        $this->assertEquals(Cart::total('price'), 922.86);
        $this->assertEquals(Cart::total('quantity', numberFormat: false), 24);

        $cartItem->price = 15.00;

        Cart::update($cartItem);

        $this->assertEquals(Cart::total('price'), 582.03);

        $cartItem->quantity = 5;

        Cart::update($cartItem);

        $this->assertEquals(Cart::total('price'), 614.51);
        $this->assertEquals(Cart::total('quantity', numberFormat: false), 26);
    }

    public function test_cart_should_apply_item_specific_tax_rate(): void
    {
        $cartItemA = Cart::add(CartItem::make(1, 'Socks', 1, 10.00));
        $cartItemB = Cart::add(CartItem::make(1, 'Socks', 1, 10.00, 6.25));

        $this->assertEquals($cartItemA->tax, 0);
        $this->assertEquals($cartItemB->tax, 6.25);
    }

    public function test_cart_should_return_calculated_tax_rate(): void
    {
        $cartItemA = Cart::add(CartItem::make(1, 'Socks', 1, 10.00));
        $cartItemB = Cart::add(CartItem::make(1, 'Socks', 1, 10.00, 6.25));

        $this->assertEquals($cartItemA->getCalculatedTaxRate(precision: 2), 0);
        $this->assertEquals($cartItemB->getCalculatedTaxRate(precision: 2), 6.25);
    }

    public function test_cart_should_delete_correct_item(): void
    {
        $cartItem = Cart::all()->random();

        Cart::remove($cartItem);

        $this->assertFalse(Cart::has($cartItem->rowId));
    }

    public function test_cart_should_dispatch_shopcart_events(): void
    {
        Event::fake();

        $cartItem = Cart::add(CartItem::make(10, 'Socks', 1, 10.00));

        Event::assertDispatched(ShopCartEvent::class, function ($event) {
            return $event->method === 'add';
        });

        Event::assertDispatched(ShopCartEvent::class, function ($event) use ($cartItem) {
            return $event->cartItem->id === $cartItem->id;
        });

        Cart::update($cartItem);

        Event::assertDispatched(ShopCartEvent::class, function ($event) {
            return $event->method === 'update';
        });

        Event::assertDispatched(ShopCartEvent::class, function ($event) use ($cartItem) {
            return $event->cartItem->id === $cartItem->id;
        });

        Cart::remove($cartItem);

        Event::assertDispatched(ShopCartEvent::class, function ($event) {
            return $event->method === 'remove';
        });

        Event::assertDispatched(ShopCartEvent::class, function ($event) use ($cartItem) {
            return $event->cartItem->id === $cartItem->id;
        });

        Cart::forget();

        Event::assertDispatched(ShopCartEvent::class, function ($event) {
            return $event->method === 'forget';
        });

        Event::assertDispatched(ShopCartEvent::class, function ($event) {
            return $event->cartItem === null;
        });
    }

    public function test_cart_should_empty(): void
    {
        Cart::forget();

        $this->assertEquals(Cart::IsEmpty(), true);
    }
}
