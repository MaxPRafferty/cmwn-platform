<?php


namespace Game\Delegator;

use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class GameDelegatorFactory
 * @package Game\Delegator
 */
class GameDelegatorFactory implements DelegatorFactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param string $name
     * @param string $requestedName
     * @param callable $callback
     *
     * @return GameDelegator
     */
    public function createDelegatorWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName, $callback)
    {
        $realService = call_user_func($callback);
        return new GameDelegator($realService);
    }
}
