# Laravel Payment

:credit_card: [Omnipay](https://github.com/thephpleague/omnipay) ServiceProvider for Laravel.

[![Tests](https://github.com/overtrue/laravel-payment/actions/workflows/tests.yml/badge.svg)](https://github.com/overtrue/laravel-payment/actions/workflows/tests.yml)
[![Latest Stable Version](https://poser.pugx.org/overtrue/laravel-payment/v/stable)](https://packagist.org/packages/overtrue/laravel-payment)
[![Total Downloads](https://poser.pugx.org/overtrue/laravel-payment/downloads)](https://packagist.org/packages/overtrue/laravel-payment)
[![License](https://poser.pugx.org/overtrue/laravel-payment/license)](https://packagist.org/packages/overtrue/laravel-payment)
[![composer.lock](https://poser.pugx.org/overtrue/laravel-payment/composerlock)](https://packagist.org/packages/overtrue/laravel-payment)

## Requirements

Version 4 requires PHP 8.3+ and Laravel 13.30+ (within Laravel 13).
Laravel 12 and earlier are not supported by this major version.

## Installing

```shell
composer require overtrue/laravel-payment:^4.0
```

The service provider and `LaravelPayment` facade alias are automatically discovered.

Publish the configuration before using the package:

```shell
php artisan vendor:publish --provider="Overtrue\LaravelPayment\ServiceProvider" --tag=config
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
])->send();

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

## Upgrading from 3.x

- Upgrade your application to PHP 8.3+ and Laravel 13.30+ before installing 4.x.
- The package now uses the Guzzle 7 adapter 1.1+ and Omnipay Common 3.5.1+.
  Check that your separately installed Omnipay gateways support these versions.
- Existing gateway names, the `payment` container alias, facade, default-option
  precedence, and per-manager gateway caching are unchanged.
- Array option values are still expanded into setter arguments. For a setter
  expecting a single array, wrap it in an outer array, for example
  `'settings' => [['key' => 'value']]`. Use positional lists for multi-argument setters.
- Unknown option setters are ignored as before. Publish and configure
  `config/payments.php`; publishing does not install a gateway driver.

## Development

```shell
composer update
composer validate --strict
composer audit --locked
composer lint
composer test
```

Tests use an in-process dummy gateway and never send payments or require credentials.
CI runs latest dependencies on PHP 8.3, 8.4 and 8.5, plus lowest secure dependencies
on PHP 8.3. Upgrade dependencies on newer PHP versions; older transitive versions
can emit upstream deprecations on PHP 8.4+.
Use current Composer 2.10+ for the abandoned-package blocking policy.
No security advisories are bypassed, and abandoned packages are blocked during resolution. The Laravel 13.30 floor excludes earlier
vulnerable Laravel 13 releases ([GHSA-jh5r-qr3c-85q8](https://github.com/laravel/framework/security/advisories/GHSA-jh5r-qr3c-85q8)).

## License

MIT
