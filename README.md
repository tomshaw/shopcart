# ShopCart 🛒

[![Latest Version](https://img.shields.io/github/release/tomshaw/shopcart.svg?style=flat-square)](https://github.com/tomshaw/shopcart/releases)
![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/tomshaw/shopcart/run-tests.yml?branch=master&style=flat-square&label=tests)
[![Total Downloads](https://img.shields.io/packagist/dt/tomshaw/shopcart.svg?style=flat-square)](https://packagist.org/packages/tomshaw/shopcart)

ShopCart is a modern easy to use [Laravel](https://laravel.com) shopping cart.

## Installation

You can install the package via composer:

```bash
composer require tomshaw/shopcart
```

Publish configuration file

```
php artisan vendor:publish --provider="TomShaw\ShopCart\ShopCartServiceProvider" --tag=config
```

## Requirements

- `Laravel 10` (https://laravel.com/) 
- `PHP 8.1` (https://php.net)

## Basic Usage

Add item to the cart.

```php
ShopCartFacade::add(
    ShopCartItem::create(id: $product->id, name: $product->name, quantity: 1, price: $product->price)
);
```

Add miscellaneous options to cart items.

```php
$cartItem = ShopCartItem::create($product->id, $product->name, 1, $product->price);

$cartItem->size = 'XL';
$cartItem->color = 'blue';

ShopCartFacade::add($cartItem);
```

Adding item specific tax rates.

> Note: This overrides the default tax rate set in the cart configuration.

```php
$cartItem = ShopCartItem::create(id: $product->id, name: $product->name, quantity: 1, price: $product->price, tax: 6.250);

ShopCartFacade::add($cartItem);
```

Updating items in the shopping cart.

> Note: Constructor properties are validated when adding or updating cart items.

```php
$cartItem = ShopCartFacade::get($rowId);

$cartItem->quantity = 2; 

$cartItem->size = '2XL';
$cartItem->color = 'black';

ShopCartFacade::update($cartItem);
```

```php
$cartItem = ShopCartFacade::where('id', '===', $id)->first();

$cartItem->quantity = 2;

$cartItem->size = '2XL';
$cartItem->color = 'black';

ShopCartFacade::update($cartItem);
```

Removing items from the shopping cart.

```php
ShopCartFacade::remove(ShopCartFacade::get($rowId));
```

```php
ShopCartFacade::remove(ShopCartFacade::where('id', '===', $id)->first());
```

Return an item from the cart by `rowId`.

```php
$cartItem = ShopCartFacade::get($rowId);
```

Checking if an item exists in the cart by `rowId`.

```php
$boolean = ShopCartFacade::has($rowId);
```

Fetching the cart collection.

```php
$cartItems = ShopCartFacade::all();
```

```php
$cartItems = ShopCartFacade::get();
```

Searching for cart items.

```php
$cartItems = ShopCartFacade::where('id', '===', $productId);
```

Checking if the shopping cart is empty or not.

```php
ShopCartFacade::isEmpty();
```

```php
ShopCartFacade::isNotEmpty();
```

Emptying the shopping cart.

```php
ShopCartFacade::forget();
```

Casting the cart as an `array` or `json`;

```php
ShopCartFacade::toArray();
```

```php
ShopCartFacade::toJson();
```

## Cart Totals.

> The total method sums properties: `tax`, `price`, `subtotal` and `quantity`. 

> Note: When no property is specified the cart total `price` is returned.

```php
$totalPrice = ShopCartFacade::total('price');
```
```php
$totalQuantity = ShopCartFacade::total(property: 'quantity', numberFormat: false);
```

```php
$subTotal = ShopCartFacade::total('subtotal');
```

```php
$totalTax = ShopCartFacade::total('tax');
```

## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). See [License File](LICENSE) for more information.
