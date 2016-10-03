<?php

namespace Api\Factory;

use Api\Listeners\FriendListener;
use Friend\Service\FriendServiceInterface;
use Suggest\Service\SuggestedServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class FriendListenerFactory
 */
class FriendListenerFactory implements FactoryInterface
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
        $suggestedService = $serviceLocator->get(SuggestedServiceInterface::class);
        return new FriendListener($friendService, $suggestedService);
    }
}
