<?php

namespace Api\V1\Rest\Reset;

use Forgot\Service\ForgotServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ResetResourceFactory
 */
class ResetResourceFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return ResetResource
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $forgotService = $serviceLocator->get(ForgotServiceInterface::class);
        return new ResetResource($forgotService);
    }
}
