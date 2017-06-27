<?php
/**
 * Created by PhpStorm.
 * User: lenovo
 * Date: 2015/11/9
 * Time: 15:39
 */

namespace App\Log\Facades;


use Illuminate\Support\Facades\Facade;

class UserLoggerRecord extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'userLoggerRecord';
    }
}