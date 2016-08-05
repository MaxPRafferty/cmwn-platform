<?php

namespace Security\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Authentication\Adapter\Http;

/**
 * Class BasicAuthAdapterFactory
 */
class BasicAuthAdapterFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config   = $serviceLocator->get('config');
        $config   = isset($config['cmwn-security']) ? $config['cmwn-security'] : [];
        $adapter  = new Http($config['basic-auth']['config']);
        $resolver = $serviceLocator->get(Http\ResolverInterface::class);

        $adapter->setBasicResolver($resolver);

        return $adapter;
    }
}
