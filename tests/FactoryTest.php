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

    public function testArrayOptionsExpandIntoSetterArguments(): void
    {
        $gateway = Factory::make('LaravelPayment_Test', [
            'pair' => ['first', 'second'],
            'settings' => [['nested' => true]],
        ]);

        $this->assertSame(['first', 'second'], $gateway->getParameters()['pair']);
        $this->assertSame(['nested' => true], $gateway->getParameters()['settings']);
    }

    public function testUnknownOptionsAreIgnored(): void
    {
        $gateway = Factory::make('LaravelPayment_Test', ['unknown_option' => 'ignored']);
        $this->assertSame([], $gateway->getParameters());
    }

    public function testInvalidDriverFails(): void
    {
        $this->expectException(\Omnipay\Common\Exception\RuntimeException::class);
        Factory::make('LaravelPayment_DoesNotExist', []);
    }
}
