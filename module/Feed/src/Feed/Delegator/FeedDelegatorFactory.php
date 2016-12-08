<?php

namespace Feed\Delegator;

use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class FeedDelegatorFactory
 * @package Feed\Delegator
 */
class FeedDelegatorFactory implements DelegatorFactoryInterface
{
    /**@inheritdoc*/
    public function createDelegatorWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName, $callback)
    {
        /**@var \Feed\Service\FeedService $feedService*/
        $feedService = call_user_func($callback);
        return new FeedDelegator($feedService);
    }
}
