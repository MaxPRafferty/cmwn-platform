<?php

namespace Security\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Authentication\Adapter\Http;

/**
 * Class BasicAuthAdapterFactory
 */
class BasicAuthAdapterFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config   = $container->get('config');
        $config   = isset($config['cmwn-security']) ? $config['cmwn-security'] : [];
        $adapter  = new Http($config['basic-auth']['config']);
        $adapter->setBasicResolver($container->get(Http\ResolverInterface::class));
        return $adapter;
    }
}
