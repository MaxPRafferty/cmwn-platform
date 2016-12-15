<?php

namespace Feed\Delegator;

use Feed\Service\FeedUserService;
use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class FeedUserDelegatorFactory
 * @package Feed\Delegator
 */
class FeedUserDelegatorFactory implements DelegatorFactoryInterface
{
    /**
     * @inheritdoc
     */
    public function createDelegatorWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName, $callback)
    {
        $feedUserService = call_user_func($callback);
        return new FeedUserDelegator($feedUserService);
    }
}
