<?php

namespace Security\Factory;

use Group\Service\UserGroupServiceInterface;
use Security\Listeners\GroupServiceListener;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class GroupServiceListenerFactory
 */
class GroupServiceListenerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var UserGroupServiceInterface $service */
        $service = $serviceLocator->get(UserGroupServiceInterface::class);
        return new GroupServiceListener($service);
    }
}
