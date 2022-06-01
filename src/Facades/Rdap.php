<?php

namespace Spatie\Rdap\Facades;

use Illuminate\Support\Facades\Facade;

class Rdap extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'rdap';
    }
}
