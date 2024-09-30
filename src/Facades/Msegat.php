<?php

namespace Alsaloul\Msegat\Facades;

use Illuminate\Support\Facades\Facade;

class Msegat extends Facade
{
    /**
     * Get the name of the bound component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'msegat';
    }
}
