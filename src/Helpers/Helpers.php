<?php

namespace TomShaw\ShopCart\Helpers;

class Helpers
{
    /**
     * Format a number with grouped thousands.
     *
     * @param  mixed  $value — The number being formatted.
     * @param  int|null  $decimals — [optional] Sets the number of decimal points.
     * @param  string|null  $decimalSeparator — [optional]
     * @param  string|null  $thousandsSeperator — [optional]
     * @return string — A formatted version of number.
     */
    public static function numberFormat(mixed $value, int $decimals = null, ?string $decimalSeparator = null, ?string $thousandsSeperator = null): string
    {
        $decimals = $decimals ?: config('shopcart.number_format.decimals');
        $decimalSeparator = $decimalSeparator ?: config('shopcart.number_format.decimal_seperator');
        $thousandsSeperator = $thousandsSeperator ?: config('shopcart.number_format.thousands_seperator');

        return number_format($value, $decimals, $decimalSeparator, $thousandsSeperator);
    }

    /**
     * Returns the rounded value of val to specified precision (number of digits after the decimal point).
     *
     * @param  int|float  $num — The value to round
     * @param  int  $precision [optional] The optional number of decimal digits to round to.
     * @param  int  $mode [optional] One of PHP_ROUND_HALF_UP, PHP_ROUND_HALF_DOWN, PHP_ROUND_HALF_EVEN, or PHP_ROUND_HALF_ODD
     * @return float — The rounded value
     */
    public static function round(int|float $num, int $precision = 0, int $mode = PHP_ROUND_HALF_UP): float
    {
        return round($num, $precision, $mode);
    }
}
