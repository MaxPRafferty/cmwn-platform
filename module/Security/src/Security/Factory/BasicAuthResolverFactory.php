<?php

namespace Security\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class BasicAuthResolverFactory
 */
class BasicAuthResolverFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config');
        $config = isset($config['cmwn-security']) ? $config['cmwn-security'] : [];

        $resolverName    = $config['basic-auth']['resolver']['resolver_class'];
        $resolverOptions = $config['basic-auth']['resolver']['options'];
        $resolver = new $resolverName();

        foreach ($resolverOptions as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($resolver, $method)) {
                $resolver->{$method}($value);
            }
        }

        return $resolver;
    }
}
