<?php

namespace Security\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class BasicAuthResolverFactory
 */
class BasicAuthResolverFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
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
