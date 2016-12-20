<?php

namespace Feed\Listener;

use Feed\Service\FeedServiceInterface;
use Feed\Service\FeedUserServiceInterface;
use Friend\Service\FriendServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class InjectFeedListenerFactory
 * @package Feed\Listener
 */
class InjectFeedListenerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return InjectFeedListener
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $feedService = $serviceLocator->get(FeedServiceInterface::class);
        $feedUserService = $serviceLocator->get(FeedUserServiceInterface::class);
        $friendService = $serviceLocator->get(FriendServiceInterface::class);
        return new InjectFeedListener($feedService, $feedUserService, $friendService);
    }
}
