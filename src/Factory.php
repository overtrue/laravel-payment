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

use Omnipay\Common\GatewayInterface;
use Omnipay\Omnipay;

/**
 * Class Factory.
 *
 * @author overtrue <i@overtrue.me>
 */
class Factory
{
    /**
     * @param string $driver
     * @param array  $options
     *
     * @return \Omnipay\Common\GatewayInterface
     */
    public static function make(string $driver, array $options)
    {
        $driver = Omnipay::create($driver);

        self::applyDriverOptions($options, $driver);

        return $driver;
    }

    /**
     * @param array $options
     *
     * @return array
     */
    protected static function formatOptions(array $options): array
    {
        $formatted = [];

        foreach ($options as $key => $value) {
            $formatted[camel_case($key)] = $value;
        }

        return $formatted;
    }

    /**
     * @param array                            $options
     * @param \Omnipay\Common\GatewayInterface $gateway
     */
    protected static function applyDriverOptions(array $options, GatewayInterface $gateway)
    {
        $gatewayMethods = get_class_methods($gateway);

        foreach (self::formatOptions($options) as $key => $value) {
            $method = 'set'.ucfirst($key);

            if (in_array($method, $gatewayMethods)) {
                call_user_func_array([$gateway, $method], (array) $value);
            }
        }
    }
}
