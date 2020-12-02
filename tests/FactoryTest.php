<?php

namespace Overtrue\LaravelPayment\Tests;

use Omnipay\Common\GatewayInterface;
use Overtrue\LaravelPayment\Factory;

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
