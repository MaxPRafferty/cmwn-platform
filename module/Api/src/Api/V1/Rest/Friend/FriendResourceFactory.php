<?php

namespace Api\V1\Rest\Friend;

use Friend\Service\FriendServiceInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class FriendResourceFactory
 */
class FriendResourceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new FriendResource($container->get(FriendServiceInterface::class));
    }
}
