<?php


namespace Overtrue\LaravelPayment;

use Illuminate\Support\Facades\Facade;

/**
 * Class Pay
 *
 * @author overtrue <i@overtrue.me>
 */
class Pay extends Facade
{
    public static function getFacadeAccessor()
    {
        return Manager::class;
    }
}