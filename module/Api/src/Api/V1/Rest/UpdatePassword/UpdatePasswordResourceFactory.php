<?php

namespace Api\V1\Rest\UpdatePassword;

use Security\Service\SecurityService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class UpdatePasswordResourceFactory
 */
class UpdatePasswordResourceFactory implements FactoryInterface
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
        /** @var SecurityService $securityService */
        $securityService = $serviceLocator->get(SecurityService::class);
        return new UpdatePasswordResource($securityService);
    }
}
