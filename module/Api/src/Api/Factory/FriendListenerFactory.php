<?php

namespace Api\Factory;

use Api\Listeners\FriendListener;
use Friend\Service\FriendServiceInterface;
use Interop\Container\ContainerInterface;
use Suggest\Service\SuggestedServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class FriendListenerFactory
 */
class FriendListenerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new FriendListener(
            $container->get(FriendServiceInterface::class),
            $container->get(SuggestedServiceInterface::class)
        );
    }
}
