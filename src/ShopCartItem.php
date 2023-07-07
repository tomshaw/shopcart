<?php

namespace TomShaw\ShopCart;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;

use TomShaw\ShopCart\Helpers\Helpers;
use TomShaw\ShopCart\Exceptions\InvalidItemException;

final class ShopCartItem
{
    /**
     * An readonly property used to items.
     *
     * @var int
     */
    public readonly int $rowId;

    /**
     * The cart item subtotal.
     *
     * @var float
     */
    public float $subTotal;

    /**
     * The cart item total tax.
     *
     * @var float
     */
    public float $totalTax;

    /**
     * The cart item total price.
     *
     * @var float
     */
    public float $totalPrice;

    /**
     * Array of cart validation rules.
     *
     * @var array
     */
    public static $rules = [
        'id' => 'required|numeric',
        'name' => 'required|string|min:3|max:255',
        'price' => 'required|numeric',
        'quantity' => 'required|numeric|min:1',
        'tax' => 'nullable|numeric',
    ];

    /**
     * Create a new cart item instance.
     *
     * @param  int  $id
     * @param  string  $name
     * @param  int  $quantity
     * @param  int  $price
     * @param  int|null  $tax
     * @param  \Illuminate\Support\Collection  $options
     * @return void
     */
    public function __construct(
        public int $id,
        public string $name,
        public int $quantity,
        public int $price,
        public float|null $tax = null,
        public Collection $options = new Collection(),
    ) {
        $this->rowId = random_int(1000000000, 9999999999);
    }

    /**
     * Dynamically access a value.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get(string $key): mixed
    {
        return $this->options->get($key);
    }

    /**
     * Dynamically set a value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function __set(string $key, mixed $value): void
    {
        $this->options->put($key, $value);
    }

    /**
     * Dynamically check if value is set.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset(string $key): bool
    {
        return $this->options->has($key);
    }

    /**
     * Dynamically unset a value.
     *
     * @param  string  $key
     * @return void
     */
    public function __unset(string $key): void
    {
        $this->options->forget($key);
    }

    /**
     * Create a new cart item.
     * 
     * @param  int  $id
     * @param  string  $name
     * @param  int  $quantity
     * @param  int  $price
     * @param  int|float|null  $tax
     * @return \TomShaw\ShopCart\ShopCartItem
     */
    public static function create(int $id, string $name, int $quantity, int $price, int|float|null $tax = null): self
    {
        $validator = self::validate($id, $name, $quantity, $price, $tax);

        return new self(...$validator->validated());
    }

    /**
     * Validate cart properties.
     * 
     * @param  int  $id
     * @param  string  $name
     * @param  int  $quantity
     * @param  int  $price
     * @param  int|null  $tax
     * @return \Illuminate\Validation\Validator
     */
    public static function validate($id, $name, $quantity, $price, $tax = null)
    {
        $validator = Validator::make(['id' => $id, 'name' => $name, 'quantity' => $quantity, 'price' => $price, 'tax' => $tax], self::$rules);

        if ($validator->fails()) {
            throw new InvalidItemException($validator->messages()->first());
        }

        return $validator;
    }

    /**
     * Validate a cart property.
     * 
     * @param  string  $name
     * @param  mixed  $value
     * @return \Illuminate\Validation\Validator
     */
    public static function validateItem($name, $value)
    {
        if (!in_array($name, array_keys(self::$rules))) {
            throw new InvalidItemException("Invalid property name.");
        }

        $validator = Validator::make([$name => $value], [$name => self::$rules[$name]]);

        if ($validator->fails()) {
            throw new InvalidItemException($validator->messages()->first());
        }

        return $validator;
    }

    /**
     * Calculates cart item totals.
     * 
     * @return void
     */
    public function process(): void
    {
        $this->subTotal = $this->getSubTotal();
        $this->totalTax = $this->getTotalTax();
        $this->totalPrice = $this->getTotalPrice();
    }

    /**
     * Get the subtotal.
     * 
     * @return float
     */
    public function getSubTotal(): float
    {
        return $this->quantity * $this->price;
    }

    /**
     * Get the total tax.
     * 
     * @return float
     */
    public function getTotalTax(): float
    {
        return $this->getSubTotal() * ($this->tax / 100);
    }

    /**
     * Get the total price.
     * 
     * @return float
     */
    public function getTotalPrice(): float
    {
        return $this->getSubTotal() + $this->getTotalTax();
    }

    /**
     * Get the calculated tax.
     * 
     * @return float
     */
    public function getCalculatedTaxRate($roundFloat = true, int $precision = 0, int $mode = PHP_ROUND_HALF_UP): float
    {
        $float = (($this->getTotalPrice() - $this->getSubTotal()) / $this->getSubTotal()) * 100;
        return $roundFloat ? Helpers::round($float, $precision, $mode) : $float;
    }
}
