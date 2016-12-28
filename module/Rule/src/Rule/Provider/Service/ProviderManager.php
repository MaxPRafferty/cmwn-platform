<?php

namespace Rule\Provider\Service;

use Rule\Provider\ProviderInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\PluginManagerInterface;

/**
 * A service container that will only build rule providers
 *
 * @method ProviderInterface build(string $name, array $options = [])
 */
class ProviderManager extends AbstractPluginManager implements PluginManagerInterface
{
    /**
     * @var string
     */
    protected $instanceOf = ProviderInterface::class;
}
