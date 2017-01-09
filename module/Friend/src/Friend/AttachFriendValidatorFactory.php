<?php

namespace Friend;

use Friend\Service\FriendServiceInterface;
use Interop\Container\ContainerInterface;
use User\Service\UserServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Class AttachFriendValidatorFactory
 */
class AttachFriendValidatorFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new AttachFriendValidator(
            $container->get(FriendServiceInterface::class),
            $container->get(UserServiceInterface::class)
        );
    }
}
