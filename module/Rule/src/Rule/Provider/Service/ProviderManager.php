<?php

namespace Rule\Provider\Service;

use Rule\Provider\ProviderInterface;
use Rule\Provider\Collection\ProviderCollectionInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\PluginManagerInterface;

/**
 * A service container that will only build rule providers
 *
 * @method ProviderInterface|ProviderCollectionInterface build(string $name, array $options = [])
 */
class ProviderManager extends AbstractPluginManager implements PluginManagerInterface
{
    /**
     * @var string
     */
    protected $instanceOf = ProviderInterface::class;
}
