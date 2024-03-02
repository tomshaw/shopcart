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
php artisan vendor:publish --provider="TomShaw\ShopCart\Providers\ShopCartServiceProvider" --tag=config
```

## Requirements

- `Laravel 10` (https://laravel.com/) 
- `PHP 8.1` (https://php.net)

## Basic Usage

Adding an item to the shopping cart.

> Note: Cart item constructor properties are validated when creating or updating cart items. 

> Note: A unique random integer `rowId` is created and used to identify cart items.

```php
use TomShaw\ShopCart\{Cart, CartItem};

$cartItem = CartItem::make(id: $product->id, name: $product->name, quantity: 1, price: $product->price);

Cart::add($cartItem);
```

Adding an item with product options to the shopping cart.

```php
$cartItem = CartItem::make($product->id, $product->name, 1, $product->price);

$cartItem->size = 'XL';
$cartItem->logo = 'Laravel Rocks';

Cart::add($cartItem);
```

Updating an item and product options in the shoping cart.

```php
$cartItem = Cart::where('id', '===', $id)->first();

$cartItem->quantity = 2;

$cartItem->size = '2XL';
$cartItem->color = 'PHP Rockz';

Cart::update($cartItem);
```

Removing an item from the shopping cart.

```php
Cart::remove(Cart::get($rowId));
```

Deleting the shopping cart after checkout.

```php
Cart::forget();
```

## Cart Totals

> Sums the properties: `tax`, `price`, `subtotal` and `quantity`.

```php
$totalPrice = Cart::total('price');
```
```php
$totalQuantity = Cart::total(property: 'quantity', numberFormat: false);
```

```php
$subTotal = Cart::total('subtotal');
```

```php
$totalTax = Cart::total('tax');
```

## Tax Rates

To set a default tax rate add the following environment variable in your application `.env`.

```sh
SHOPCART_DEFAULT_TAXRATE=9.547
```

Applying item specific tax rates.

> You can easily apply item specific tax at run time. 

```php
use TomShaw\ShopCart\{Cart, CartItem};

Cart::add(CartItem::make(tax: 6.250, ...));
```

## Number Formatting

Number formating is handled by adding the following environment variables to your application `.env`.

```sh
SHOPCART_DECIMALS=2
SHOPCART_DECIMAL_SEPARATOR="."
SHOPCART_THOUSANDS_SEPARATOR=","
```

### Proxy Methods

Get item from collection by `rowId`.

```php
$cartItem = Cart::get($rowId);
```

Check if cart item exists by `rowId`.

```php
$boolean = Cart::has($rowId);
```

Get cart as collection or array.

```php
$cartItems = Cart::all(bool $toArray = false);
```

Searching for specific cart items.

```php
$cartItems = Cart::where('id', '===', $productId);
```

Check if the cart is empty or not.

```php
Cart::isEmpty();
```

```php
Cart::isNotEmpty();
```

Casting the cart as an `array` or `json`;

```php
Cart::toArray();
```

```php
Cart::toJson();
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
