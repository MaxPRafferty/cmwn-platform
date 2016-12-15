<?php

namespace Api\V1\Rest\GroupReset;

use Security\Service\SecurityServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class GroupResetResourceFactory
 * @package Api\V1\Rest\GroupReset
 */
class GroupResetResourceFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return GroupResetResource
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $securityService = $serviceLocator->get(SecurityServiceInterface::class);
        return new GroupResetResource($securityService);
    }
}
