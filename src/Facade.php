<?php


namespace Overtrue\LaravelPayment;

use Illuminate\Support\Facades\Facade as LaravelFacade;

/**
 * Class Facade
 *
 * @author overtrue <i@overtrue.me>
 */
class Facade extends LaravelFacade
{
    public static function getFacadeAccessor()
    {
        return Manager::class;
    }
}