<?php

namespace Spatie\Rdap\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Spatie\Rdap\Rdap
 */
class Rdap extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-rdap';
    }
}
