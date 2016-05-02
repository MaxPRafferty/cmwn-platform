<?php

namespace Friend;

use Friend\Service\FriendServiceInterface;
use User\Service\UserServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class AttachFriendValidatorFactory
 */
class AttachFriendValidatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceLocator = $serviceLocator instanceof ServiceLocatorAwareInterface
            ? $serviceLocator->getServiceLocator()
            : $serviceLocator;
        
        /** @var UserServiceInterface $userService */
        /** @var FriendServiceInterface $friendService */
        $userService   = $serviceLocator->get(UserServiceInterface::class);
        $friendService = $serviceLocator->get(FriendServiceInterface::class);

        return new AttachFriendValidator($friendService, $userService);
    }
}
