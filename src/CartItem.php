<?php

namespace TomShaw\ShopCart;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use TomShaw\ShopCart\Exceptions\InvalidItemException;
use TomShaw\ShopCart\Helpers\Helpers;

final class CartItem
{
    public readonly int $rowId;

    public float $subTotal {
        get => $this->quantity * $this->price;
    }

    public float $totalTax {
        get => $this->subTotal * (($this->tax ?? 0) / 100);
    }

    public float $totalPrice {
        get => $this->subTotal + $this->totalTax;
    }

    public static $rules = [
        'id' => 'required|numeric',
        'name' => 'required|string|min:3|max:255',
        'price' => 'required|numeric',
        'quantity' => 'required|numeric|min:1',
        'tax' => 'nullable|numeric',
    ];

    public function __construct(
        public int $id,
        public string $name,
        public int $quantity,
        public float $price,
        public ?float $tax = null,
        public Collection $options = new Collection,
    ) {
        $this->rowId = random_int(1000000000, 9999999999);
    }

    public static function make(int $id, string $name, int $quantity, float $price, ?float $tax = null): self
    {
        $validator = self::validate($id, $name, $quantity, $price, $tax);

        return new self(...$validator->validated());
    }

    public static function validate($id, $name, $quantity, $price, $tax = null)
    {
        $validator = Validator::make(['id' => $id, 'name' => $name, 'quantity' => $quantity, 'price' => $price, 'tax' => $tax], self::$rules);

        if ($validator->fails()) {
            throw new InvalidItemException($validator->messages()->first());
        }

        return $validator;
    }

    public static function validateItem($name, $value)
    {
        if (! in_array($name, array_keys(self::$rules))) {
            throw new InvalidItemException('Invalid property name.');
        }

        $validator = Validator::make([$name => $value], [$name => self::$rules[$name]]);

        if ($validator->fails()) {
            throw new InvalidItemException($validator->messages()->first());
        }

        return $validator;
    }

    public function getCalculatedTaxRate($roundFloat = true, int $precision = 0, int $mode = PHP_ROUND_HALF_UP): float
    {
        $float = (($this->totalPrice - $this->subTotal) / $this->subTotal) * 100;

        return $roundFloat ? Helpers::round($float, $precision, $mode) : $float;
    }
}
