<?php

namespace Forgot\Service;

use Security\Service\SecurityServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ForgotServiceFactory
 */
class ForgotServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var SecurityServiceInterface $service */
        $service = $serviceLocator->get(SecurityServiceInterface::class);
        return new ForgotService($service);
    }
}
