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

use Illuminate\Config\Repository;
use Illuminate\Support\Collection;
use Omnipay\Common\GatewayInterface;

/**
 * Class Manager.
 *
 * @author overtrue <i@overtrue.me>
 */
class Manager
{
    /**
     * @var Collection
     */
    protected $config;

    /**
     * @var array
     */
    protected $gateways = [];

    /**
     * Manager constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = new Repository($config);
    }

    /**
     * @param string|null $name
     *
     * @return \Omnipay\Common\GatewayInterface
     */
    public function gateway(string $name = null): GatewayInterface
    {
        if (empty($name)) {
            $name = $this->getDefaultGateway();
        }

        if (empty($this->gateways[$name])) {
            $driver = $this->config->get("gateways.{$name}.driver");

            if (is_null($driver)) {
                throw new \InvalidArgumentException(sprintf('No omnipay driver found for gateway "%s".', $name));
            }

            $this->gateways[$name] = Factory::make($driver, $this->getGatewayOptions($name));
        }

        return $this->gateways[$name];
    }

    /**
     * @param string $name
     *
     * @return array
     */
    public function getGatewayOptions(string $name): array
    {
        return array_merge($this->config->get('default_options', []), $this->config->get("gateways.{$name}.options", []));
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    public function getDefaultGateway()
    {
        $name = $this->config->get('default_gateway');

        if (empty($name)) {
            throw new \Exception('No default gateway configured.');
        }

        return $name;
    }

    /**
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        return call_user_func([$this->gateway(), $method], ...$args);
    }
}
