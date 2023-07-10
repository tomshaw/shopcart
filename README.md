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

Adding an item to the shopping cart.

> Note: Cart item constructor properties are validated when creating or updating cart items. 

> Note: A unique random integer `rowId` is created used to identify cart items.

```php
$cartItem = ShopCartItem::create($product->id, $product->name, 1, $product->price);

ShopCartFacade::add($cartItem);
```

Updating an item and option attributes in the shoping cart.

```php
$cartItem = ShopCartFacade::where('id', '===', $id)->first();

$cartItem->quantity = 2;

$cartItem->size = '2XL';
$cartItem->color = 'black';

ShopCartFacade::update($cartItem);
```

Removing an item from the shopping cart.

```php
ShopCartFacade::remove(ShopCartFacade::get($rowId));
```

Deleting the shopping cart after checkout.

```php
ShopCartFacade::forget();
```

## Cart Totals

> Sums the properties: `tax`, `price`, `subtotal` and `quantity`.

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

## Tax Rates

To set a default tax rate create the following environment variable in your applications `.env`.

```sh
SHOPCART_DEFAULT_TAXRATE=9.547
```

Applying item specific taxe rates.

> Note: This overrides the default tax rate set in your cart configuration.

```php
$cartItem = ShopCartItem::create(tax: 6.250, ...);

ShopCartFacade::add($cartItem);
```

## Number Formatting

Number formating is handled by adding the following environment variables to your applications `.env`.

```sh
SHOPCART_DECIMALS=2
SHOPCART_DECIMAL_SEPARATOR="."
SHOPCART_THOUSANDS_SEPARATOR=","
```

### Proxy Methods

Fetching an item from the shopping cart by `rowId`.

```php
$cartItem = ShopCartFacade::get($rowId);
```

Checking if an item exists in the shopping cart by `rowId`.

```php
$boolean = ShopCartFacade::has($rowId);
```

Fetch the cart collection as array.

```php
$cartItems = ShopCartFacade::all();
```

Fetch the cart collection as collection.

```php
$cartItems = ShopCartFacade::get();
```

Searching for specific cart items.

```php
$cartItems = ShopCartFacade::where('id', '===', $productId);
```

Check if the cart is empty or not.

```php
ShopCartFacade::isEmpty();
```

```php
ShopCartFacade::isNotEmpty();
```

Casting the cart as an `array` or `json`;

```php
ShopCartFacade::toArray();
```

```php
ShopCartFacade::toJson();
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
