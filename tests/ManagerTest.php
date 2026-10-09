<?php

namespace Overtrue\LaravelPayment\Tests;

use Omnipay\Common\GatewayInterface;
use Omnipay\LaravelPayment\TestGateway;
use Overtrue\LaravelPayment\Manager;

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

    public function testDefaultOptionsPrecedenceAndIndependentGatewayCache(): void
    {
        $manager = new Manager([
            'default_gateway' => 'first',
            'default_options' => ['username' => 'default', 'test_mode' => true],
            'gateways' => [
                'first' => ['driver' => 'LaravelPayment_Test', 'options' => ['username' => 'override']],
                'second' => ['driver' => 'LaravelPayment_Test'],
            ],
        ]);

        $this->assertSame(['username' => 'override', 'test_mode' => true], $manager->getGatewayOptions('first'));
        $this->assertSame('override', $manager->gateway()->getUsername());
        $this->assertTrue($manager->gateway()->getTestMode());
        $this->assertSame('default', $manager->gateway('second')->getUsername());
        $this->assertNotSame($manager->gateway('first'), $manager->gateway('second'));
        $this->assertSame($manager->gateway('first'), $manager->gateway(null));
        $this->assertSame($manager->gateway('first'), $manager->gateway(''));
        $this->assertSame($manager->gateway('second'), $manager->gateway('second'));
    }

    public function testDynamicCallsForwardEveryArgumentUnchanged(): void
    {
        $manager = new Manager([
            'default_gateway' => 'dummy',
            'gateways' => ['dummy' => ['driver' => 'LaravelPayment_Test']],
        ]);

        $this->assertSame([], $manager->forwardArguments());
        $this->assertSame(['one', ['two' => 2], null, false], $manager->forwardArguments('one', ['two' => 2], null, false));
    }
}
