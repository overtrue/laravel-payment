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

/**
 * Class TestCase.
 *
 * @author overtrue <i@overtrue.me>
 */
class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * Tear down the test case.
     */
    public function tearDown()
    {
        parent::tearDown();

        if ($container = \Mockery::getContainer()) {
            $this->addToAssertionCount($container->Mockery_getExpectationCount());
        }

        \Mockery::close();
    }
}
