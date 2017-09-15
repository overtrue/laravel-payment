<?php

/*
 * This file is part of the overtrue/laravel-payment.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

return [
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
];
