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
use Omnipay\LaravelPayment\TestGateway;
use Overtrue\LaravelPayment\Manager;

/**
 * Class ManagerTest.
 *
 * @author overtrue <i@overtrue.me>
 */
class ManagerTest extends TestCase
{
    public function testGateway()
    {
        $manager = new Manager([
            'gateways' => [
                'foo' => [
                    'driver' => 'LaravelPayment_Test',
                    'options' => [
                        'username' => 'overtrue',
                        'test_mode' => true,
                    ],
                ],
            ],
        ]);
        $gateway = $manager->gateway('foo');
        $this->assertInstanceOf(GatewayInterface::class, $gateway);
        $this->assertSame($gateway, $manager->gateway('foo'));
    }

    public function testMakeInvalidGateway()
    {
        $manager = new Manager([
            'gateways' => [
                'foo' => [
                    'options' => [
                        'username' => 'overtrue',
                        'test_mode' => true,
                    ],
                ],
            ],
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('No omnipay driver found for gateway "foo".');
        $manager->gateway('foo');
    }

    public function testGetDefaultGateway()
    {
        $manager = new Manager([
            'default_gateway' => 'foo',
            'gateways' => [
                'foo' => [
                    'driver' => 'LaravelPayment_Test',
                    'options' => [
                        'username' => 'overtrue',
                        'test_mode' => true,
                    ],
                ],
            ],
        ]);

        $this->assertInstanceOf(TestGateway::class, $manager->gateway());
    }

    public function testGetDefaultGatewayWithWrongConfig()
    {
        $manager = new Manager([
            'gateways' => [
                'foo' => [
                    'driver' => 'LaravelPayment_Test',
                    'options' => [
                        'username' => 'overtrue',
                        'test_mode' => true,
                    ],
                ],
            ],
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No default gateway configured.');
        $manager->gateway();
    }

    public function testCall()
    {
        $manager = new Manager([
            'default_gateway' => 'foo',
            'gateways' => [
                'foo' => [
                    'driver' => 'LaravelPayment_Test',
                    'options' => [
                        'username' => 'overtrue',
                        'test_mode' => true,
                    ],
                ],
            ],
        ]);

        $this->assertSame([
            'username' => 'overtrue',
            'testMode' => true,
        ], $manager->getParameters());
    }
}
