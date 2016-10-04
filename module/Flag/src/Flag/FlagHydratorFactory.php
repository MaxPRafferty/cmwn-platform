<?php

namespace Flag;

use User\Service\UserServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class FlagHydratorFactory
 * @package Flag
 */
class FlagHydratorFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return FlagHydrator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $userService = $serviceLocator->get(UserServiceInterface::class);
        return new FlagHydrator($userService);
    }
}
