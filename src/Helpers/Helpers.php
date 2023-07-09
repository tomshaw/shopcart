<?php

namespace TomShaw\ShopCart\Helpers;

class Helpers
{
    /**
     * Format a number with grouped thousands.
     *
     * @param  float  $num — The number being formatted.
     * @param  int  $decimals — [optional] Sets the number of decimal points.
     * @param  string|null  $decimal_separator — [optional]
     * @param  string|null  $thousands_separator — [optional]
     * @return string — A formatted version of number.
     */
    public static function numberFormat($value, int $decimals = null, ?string $decimalSeperator = null, ?string $thousandsSeperator = null): string
    {
        $decimals = $decimals ?: config('shopcart.number_format.decimals');
        $decimalSeperator = $decimalSeperator ?: config('shopcart.number_format.decimal_seperator');
        $thousandsSeperator = $thousandsSeperator ?: config('shopcart.number_format.thousands_seperator');

        return number_format($value, $decimals, $decimalSeperator, $thousandsSeperator);
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
