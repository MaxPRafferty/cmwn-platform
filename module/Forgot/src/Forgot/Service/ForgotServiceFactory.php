<?php

namespace Forgot\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ForgotServiceFactory
 * @codeCoverageIgnore
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
        /** @var \Security\Service\SecurityServiceInterface $service */
        $service = $serviceLocator->get('Security\Service\SecurityService');
        return new ForgotService($service);
    }
}
