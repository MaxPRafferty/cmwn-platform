<?php

namespace Api\V1\Rest\FlipUser;

use Flip\Service\FlipUserServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class FlipUserResourceFactory
 */
class FlipUserResourceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var FlipUserServiceInterface $flipUserService */
        $flipUserService = $serviceLocator->get(FlipUserServiceInterface::class);
        return new FlipUserResource($flipUserService);
    }
}
