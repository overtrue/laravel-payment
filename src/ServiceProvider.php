<?php

/*
 * This file is part of the overtrue/laravel-payment.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Overtrue\LaravelPayment;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

/**
 * Class ServiceProvider.
 *
 * @author overtrue <i@overtrue.me>
 */
class ServiceProvider extends LaravelServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = true;

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/payments.php',
        ], 'config');
    }

    public function register()
    {
        $this->app->singleton(Manager::class, function ($app) {
            return new Manager($app->config->get('payments', []));
        });

        $this->app->alias(Manager::class, 'payment');
    }

    /**
     * @return array
     */
    public function provides()
    {
        return [Manager::class, 'payment'];
    }
}
