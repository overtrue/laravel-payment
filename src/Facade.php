<?php


namespace Overtrue\LaravelPayment;

use Illuminate\Support\Facades\Facade as LaravelFacade;

class Facade extends LaravelFacade
{
    public static function getFacadeAccessor()
    {
        return Manager::class;
    }
}
