<?php

namespace Security\Guard;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Creates a Reset Password Guard
 *
 * @package Security\Guard
 */
class ResetPasswordGuardFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ResetPasswordGuard($serviceLocator->get('authentication'));
    }
}
