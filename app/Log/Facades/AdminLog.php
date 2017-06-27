<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/11/4
 * Time: 14:47
 */

namespace App\Log\Facades;


use Illuminate\Support\Facades\Facade;

class AdminLog extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'adminLogger';
    }
}