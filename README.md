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

### Publish configuration file

```
php artisan vendor:publish --provider="TomShaw\ShopCart\ShopCartServiceProvider" --tag=config
```

## Requirements

- `Laravel 10` (https://laravel.com/) 
- `PHP 8.1` (https://php.net)

## Basic Usage

Add item to the cart.

```php
$cartItem = ShopCartItem::create(id: $product->id, name: $product->name, quantity: 1, price: $product->price);

ShopCart::add($cartItem);
```

Add item option attributes.

```php
$cartItem = ShopCartItem::create($product->id, $product->name, 1, $product->price);

$cartItem->size = 'XL';
$cartItem->color = 'blue';

ShopCart::add($cartItem);
```

Add specified item tax rate.

> Note: This overrides the default tax rate set in the cart configuration.

```php
$cartItem = ShopCartItem::create(id: $product->id, name: $product->name, quantity: 1, price: $product->price, tax: 8.25);

ShopCart::add($cartItem);
```

Updating an item in the shopping cart.

> Note: Constructor properties are auto-validated when adding or updating cart items.

```php
$cartItem = ShopCart::get($rowId);

$cartItem->quantity = 2;
$cartItem->size = '2XL';
$cartItem->color = 'black';

ShopCart::update($cartItem);
```

```php
$cartItem = ShopCart::where('id', '===', $id)->first();

$cartItem->quantity = 2;
$cartItem->size = '2XL';
$cartItem->color = 'black';

ShopCart::update($cartItem);
```

Remove item from the shopping cart.

```php
ShopCart::remove(ShopCart::get($rowId));
```

```php
ShopCart::remove(ShopCart::where('id', '===', $id)->first());
```

Return an item from the cart by `rowId`.

```php
$cartItem = ShopCart::get($rowId);
```

Checking if an item exists in the cart by `rowId`.

```php
$boolean = ShopCart::has($rowId);
```

Returning the cart collection.

```php
$cartItems = ShopCart::all();
```

```php
$cartItems = ShopCart::get();
```

Searching for cart items.

```php
$cartItems = ShopCart::where('id', '===', $productId);
```

Checking if cart is empty or not.

```php
ShopCart::isEmpty();
```

```php
ShopCart::isNotEmpty();
```

Empty the shopping cart.

```php
ShopCart::forget();
```

Output cart as `array` or `json`;

```php
ShopCart::toArray();
```

```php
ShopCart::toJson();
```

## Cart Totals.

> The total method sums properties: `tax`, `price`, `subtotal` and `quantity`. When no property is specified the total price will be returned.

```php
$totalPrice = ShopCart::total('price');
```
```php
$totalQuantity = ShopCart::total(property: 'quantity', numberFormat: false);
```

```php
$subTotal = ShopCart::total('subtotal');
```

```php
$totalTax = ShopCart::total('tax');
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
