<?php

namespace TomShaw\ShopCart\Tests;

use TomShaw\ShopCart\Tests\TestCase;

use TomShaw\ShopCart\ShopCartItem;
use TomShaw\ShopCart\Facades\ShopCartFacade;

class ShopCartTest extends TestCase
{
    protected ShopCartItem $cartItemA;
    protected ShopCartItem $cartItemB;
    protected ShopCartItem $cartItemC;
    protected ShopCartItem $cartItemD;
    protected ShopCartItem $cartItemE;

    public function setup(): void
    {
        parent::setUp();

        $this->cartItemA = ShopCartFacade::add(ShopCartItem::create(id: 1, name: 'Socks', quantity: 1, price: 10.00));
        $this->cartItemB = ShopCartFacade::add(ShopCartItem::create(id: 2, name: 'Shoes', quantity: 2, price: 20.00));
        $this->cartItemC = ShopCartFacade::add(ShopCartItem::create(id: 3, name: 'Pants', quantity: 3, price: 30.00, tax: 7.25));
        $this->cartItemD = ShopCartFacade::add(ShopCartItem::create(id: 4, name: 'Shirt', quantity: 4, price: 40.00, tax: 5.30));
        $this->cartItemE = ShopCartFacade::add(ShopCartItem::create(id: 5, name: 'Ray-Bans', quantity: 5, price: 50.00, tax: 6.25));
    }

    public function test_cart_get_method_returns_correct_data(): void
    {
        $this->assertEquals(ShopCartFacade::get($this->cartItemA->rowId)->id, $this->cartItemA->id);
        $this->assertEquals(ShopCartFacade::get($this->cartItemB->rowId)->name, $this->cartItemB->name);
        $this->assertEquals(ShopCartFacade::get($this->cartItemC->rowId)->quantity, $this->cartItemC->quantity);
        $this->assertEquals(ShopCartFacade::get($this->cartItemC->rowId)->price, $this->cartItemC->price);
    }

    public function test_cart_has_method_returns_correct_boolean_value(): void
    {
        $cartItem = ShopCartFacade::get()->random();

        $this->assertTrue(ShopCartFacade::has($cartItem->rowId));
        $this->assertFalse(ShopCartFacade::has(10));
    }
    
    public function test_cart_update_method_returns_cart_totals(): void
    {
        $cartItem = ShopCartFacade::where('id', '===', 2)->first();

        $cartItem->quantity = 3;

        ShopCartFacade::update($cartItem);

        $this->assertEquals(ShopCartFacade::total('price'), 606.41);
        $this->assertEquals(ShopCartFacade::total('quantity', numberFormat: false), 16);

        $cartItem->price = 15.00;

        ShopCartFacade::update($cartItem);

        $this->assertEquals(ShopCartFacade::total('price'), 590.17);

        $cartItem->quantity = 5;

        ShopCartFacade::update($cartItem);

        $this->assertEquals(ShopCartFacade::total('price'), 622.64);
        $this->assertEquals(ShopCartFacade::total('quantity', numberFormat: false), 18);
    }

    public function test_cart_item_can_apply_tax_rate(): void
    {
        $cartItemA = ShopCartFacade::add(ShopCartItem::create(1, 'Socks', 1, 10.00));
        $cartItemB = ShopCartFacade::add(ShopCartItem::create(1, 'Socks', 1, 10.00, 6.25));

        $this->assertEquals($cartItemA->tax, 8.25);
        $this->assertEquals($cartItemB->tax, 6.25);
    }

    public function test_cart_returns_calculated_tax_rate(): void
    {
        $cartItemA = ShopCartFacade::add(ShopCartItem::create(1, 'Socks', 1, 10.00));
        $cartItemB = ShopCartFacade::add(ShopCartItem::create(1, 'Socks', 1, 10.00, 6.25));

        $this->assertEquals($cartItemA->getCalculatedTaxRate(precision: 2), 8.25);
        $this->assertEquals($cartItemB->getCalculatedTaxRate(precision: 2), 6.25);
    }

    public function test_cart_remove_method_deletes_correct_item(): void
    {
        $cartItem = ShopCartFacade::get()->random();

        ShopCartFacade::remove($cartItem);

        $this->assertFalse(ShopCartFacade::has($cartItem->rowId));
    }

    public function test_cart_empty_method_removes_all_items(): void
    {
        ShopCartFacade::forget();

        $this->assertEquals(ShopCartFacade::IsEmpty(), true);
    }
}
