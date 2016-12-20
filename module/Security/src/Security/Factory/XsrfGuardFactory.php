<?php

namespace Security\Factory;

use Interop\Container\ContainerInterface;
use Security\Guard\XsrfGuard;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class XsrfGuardListenerFactory
 */
class XsrfGuardFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        $config = isset($config['cmwn-security']) ? $config['cmwn-security'] : [];
        return new XsrfGuard($config);
    }
}
