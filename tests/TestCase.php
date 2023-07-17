<?php

namespace TomShaw\ShopCart\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use TomShaw\ShopCart\Providers\ShopCartServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [ShopCartServiceProvider::class];
    }
}
