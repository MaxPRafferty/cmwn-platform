<?php

namespace Feed\Listener;

use Feed\Service\FeedServiceInterface;
use Feed\Service\FeedUserServiceInterface;
use Friend\Service\FriendServiceInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class InjectFeedListenerFactory
 * @package Feed\Listener
 */
class InjectFeedListenerFactory implements FactoryInterface
{
    /**
     * @inheritdoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new InjectFeedListener(
            $container->get(FeedServiceInterface::class),
            $container->get(FeedUserServiceInterface::class),
            $container->get(FriendServiceInterface::class)
        );
    }
}
