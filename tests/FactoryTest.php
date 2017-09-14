<?php

/*
 * This file is part of the overtrue/laravel-payment.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Overtrue\LaravelPayment\Tests;

use Omnipay\Common\GatewayInterface;
use Overtrue\LaravelPayment\Factory;

/**
 * Class FactoryTest.
 *
 * @author overtrue <i@overtrue.me>
 */
class FactoryTest extends TestCase
{
    public function testMake()
    {
        $gateway = Factory::make('LaravelPayment_Test', []);

        $this->assertInstanceOf(GatewayInterface::class, $gateway);
        $this->assertEmpty($gateway->getParameters());

        // snake case
        $gateway = Factory::make('LaravelPayment_Test', [
            'test_mode' => true,
            'username' => 'overtrue',
        ]);

        $this->assertSame([
            'testMode' => true,
            'username' => 'overtrue',
        ], $gateway->getParameters());

        // snake case
        $gateway = Factory::make('LaravelPayment_Test', [
            'testMode' => true,
        ]);

        $this->assertSame(['testMode' => true], $gateway->getParameters());
    }
}
