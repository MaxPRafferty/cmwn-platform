<?php

namespace Security\Factory;

use Security\Guard\CsrfGuard;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class CsrfGuardFactory
 */
class CsrfGuardFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config');
        $config = isset($config['cmwn-security']) ? $config['cmwn-security'] : [];

        return new CsrfGuard($config);
    }
}
