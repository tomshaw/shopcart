<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default tax rate
    |--------------------------------------------------------------------------
    |
    | Sets default tax percentage. Can be overridden when generating new cart items.
    |
    */

    'default' => [
        'tax' => env('SHOPCART_DEFAULT_TAX', 8.25),
    ],


    /*
    |--------------------------------------------------------------------------
    | Default number format
    |--------------------------------------------------------------------------
    |
    | Set applied number format with grouped thousands.
    |
    */

    'number_format' => [
        'decimals' => env('SHOPCART_DECIMALS', 2),
        'decimal_seperator' => env('SHOPCART_DECIMAL_SEPARATOR', '.'),
        'thousands_seperator' => env('SHOPCART_THOUSANDS_SEPARATOR', ','),
    ],

];