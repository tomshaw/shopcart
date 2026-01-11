# ShopCart 🛒

![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/tomshaw/shopcart/tests.yml?branch=master&style=flat-square&label=tests)
![issues](https://img.shields.io/github/issues/tomshaw/shopcart?style=flat&logo=appveyor)
![forks](https://img.shields.io/github/forks/tomshaw/shopcart?style=flat&logo=appveyor)
![stars](https://img.shields.io/github/stars/tomshaw/shopcart?style=flat&logo=appveyor)
[![GitHub license](https://img.shields.io/github/license/tomshaw/shopcart)](https://github.com/tomshaw/shopcart/blob/master/LICENSE)

ShopCart is a modern easy to use [Laravel](https://laravel.com) shopping cart...

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

- PHP 8.4+
- Laravel 12

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

$cartItem->options['size'] = 'XL';
$cartItem->options['logo'] = 'Laravel Rocks';

Cart::add($cartItem);
```

Accessing product options.

```php
// Array access
$size = $cartItem->options['size'];

// Or using Collection methods
$size = $cartItem->options->get('size');
$cartItem->options->put('color', 'blue');
$hasSize = $cartItem->options->has('size');
```

Updating an item and product options in the shopping cart.

```php
$cartItem = Cart::all()->where('id', '===', $id)->first();

$cartItem->quantity = 5;
$cartItem->options['size'] = '2XL';

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

## Computed Properties

Cart items automatically calculate `subTotal`, `totalTax`, and `totalPrice`. These values are computed on-demand and are always accurate.

```php
$cartItem = CartItem::make(id: 1, name: 'Product', quantity: 2, price: 100.00, tax: 8.5);

// Automatically computed properties
echo $cartItem->subTotal;    // 200.00 (quantity * price)
echo $cartItem->totalTax;    // 17.00 (subTotal * tax / 100)
echo $cartItem->totalPrice;  // 217.00 (subTotal + totalTax)

// Values update automatically when properties change
$cartItem->quantity = 3;
echo $cartItem->subTotal;    // 300.00 (automatically recalculated)
```

## Tax Rates

To set a default tax rate add the following environment variable in your application `.env`.

```sh
SHOPCART_DEFAULT_TAXRATE=9.547
```

You can easily apply item specific tax rates at run time. 

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

## Cart Methods

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

Use Laravel Collection methods for advanced operations.

```php
// Search for specific cart items
$cartItems = Cart::all()->where('id', '===', $productId);

// Check if cart is empty
$isEmpty = Cart::all()->isEmpty();
$isNotEmpty = Cart::all()->isNotEmpty();

// Filter, map, or use any Collection method
$total = Cart::all()->sum('totalPrice');
$firstItem = Cart::all()->first();
```

Casting the cart as an `array` or `json`;

```php
Cart::toArray();
```

```php
Cart::toJson();
```

## Changelog

For changes made to the project, see the [Changelog](CHANGELOG.md).

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). See [License File](LICENSE) for more information.
