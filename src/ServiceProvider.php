<?php

namespace Overtrue\LaravelPayment;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * @var bool
     */
    protected $defer = true;

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/payments.php' => config_path('payments.php'),
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
