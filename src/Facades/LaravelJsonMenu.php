<?php

namespace Atthakasem\LaravelJsonMenu\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelJsonMenu extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-json-menu';
    }
}
