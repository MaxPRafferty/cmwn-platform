<?php

namespace Api\V1\Rest\Friend;

use Friend\Service\FriendServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class FriendResourceFactory
 */
class FriendResourceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var FriendServiceInterface $friendService */
        $friendService = $serviceLocator->get(FriendServiceInterface::class);
        return new FriendResource($friendService);
    }
}
