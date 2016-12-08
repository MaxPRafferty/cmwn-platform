<?php

namespace Api\Factory;

use Api\Listeners\InjectUserListener;
use User\Service\UserServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class InjectUserListenerFactory
 * @package Api\Factory
 */
class InjectUserListenerFactory implements FactoryInterface
{
    /**
     * @inheritdoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /**@var UserServiceInterface $userService*/
        $userService = $serviceLocator->get(UserServiceInterface::class);
        return new InjectUserListener($userService);
    }
}
