# Laravel Payment

:credit_card: [Omnipay](https://github.com/thephpleague/omnipay) ServiceProvider for Laravel.

[![Build Status](https://travis-ci.org/overtrue/laravel-payment.svg?branch=master)](https://travis-ci.org/overtrue/laravel-payment)
[![Latest Stable Version](https://poser.pugx.org/overtrue/laravel-payment/v/stable)](https://packagist.org/packages/overtrue/laravel-payment)
[![Total Downloads](https://poser.pugx.org/overtrue/laravel-payment/downloads)](https://packagist.org/packages/overtrue/laravel-payment)
[![License](https://poser.pugx.org/overtrue/laravel-payment/license)](https://packagist.org/packages/overtrue/laravel-payment)
[![composer.lock](https://poser.pugx.org/overtrue/laravel-payment/composerlock)](https://packagist.org/packages/overtrue/laravel-payment)

## Installing

```shell
$ composer require overtrue/laravel-payment -v
```

After updated composer, if you are using laravel version < 5.5, you need to register service provider: 

```php
// config/app.php

    'providers' => [
        //...
        Overtrue\LaravelPayment\ServiceProvider::class,
    ],
```

And publish the config file: 

```shell
$ php artisan vendor:publish --provider=Overtrue\\LaravelPayment\\ServiceProvider
```

if you want to use facade mode, you can register a facade name what you want to use, for example `LaravelPayment`:

```php
// config/app.php

    'aliases' => [
        'LaravelPayment' => Overtrue\LaravelPayment\Facade::class, // This is default in laravel 5.5
    ],
```

### configuration 

```php
// config/payments.php

    // The default gateway name which configured in `gateways` section.
    'default_gateway' => 'paypal',

    // The default options for every gateways.
    'default_options' => [
        'test_mode' => true,
        // ...
    ],

    /*
     * The gateways, you can config option by camel case or snake_case name.
     *
     * the option name is followed from gateway class, for example:
     *
     * $gateway->setMchId('overtrue');
     *
     * you can configured as:
     *  'mch_id' => 'overtrue',
     * or:
     *  'mchId' => 'overtrue',
     */
    'gateways' => [
        'paypal' => [
            'driver' => 'PayPal_Express',
            'options' => [
                'username' => env('PAYPAL_USERNAME'),
                'password' => env('PAYPAL_PASSWORD'),
                'signature' => env('PAYPAL_SIGNATURE'),
                'test_mode' => env('PAYPAL_TEST_MODE'),
            ],
        ],
        // other gateways
    ],
```

### install payment gateways

You need to install the gateway you want to use: [omnipay#payment-gateways](https://github.com/thephpleague/omnipay#payment-gateways)

## Usage

Gateway instance:

```php
LaravelPayment::gateway('GATEWAY NAME'); // GATEWAY NAME is key name of `gateways` configuration.
LaravelPayment::gateway('alipay');
LaravelPayment::gateway('paypal');
```

Using default gateway:

```php
LaravelPayment::purchase(...);
```

Example:

```php
$formData = [
    'number' => '4242424242424242', 
    'expiryMonth' => '6', 
    'expiryYear' => '2030', 
    'cvv' => '123'
];

$response = LaravelPayment::purchase([
    'amount' => '10.00', 
    'currency' => 'USD', 
    'card' => $formData,
))->send();

if ($response->isRedirect()) {
    // redirect to offsite payment gateway
    $response->redirect();
} elseif ($response->isSuccessful()) {
    // payment was successful: update database
    print_r($response);
} else {
    // payment failed: display message to customer
    echo $response->getMessage();
}
```

For more use about [Omnipay](https://github.com/omnipay/omnipay), please refer to [Omnipay Official Home Page](http://omnipay.thephpleague.com/)

## PHP 扩展包开发

> 想知道如何从零开始构建 PHP 扩展包？
>
> 请关注我的实战课程，我会在此课程中分享一些扩展开发经验 —— [《PHP 扩展包实战教程 - 从入门到发布》](https://learnku.com/courses/creating-package)

## License

MIT
