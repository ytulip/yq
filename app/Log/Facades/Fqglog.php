<?php
namespace App\Log\Facades;

use Illuminate\Support\Facades\Facade;

class Fqglog extends Facade
{

    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'fqglog';
    }
}