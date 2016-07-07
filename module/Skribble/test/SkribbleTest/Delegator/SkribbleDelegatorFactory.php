<?php

namespace SkribbleTest\Delegator;

use Skribble\Delegator\SkribbleServiceDelegator;
use Skribble\Service\SkribbleService;
use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class SkribbleDelegatorFactory
 */
class SkribbleDelegatorFactory implements DelegatorFactoryInterface
{
    /**
     * @inheritdoc
     */
    public function createDelegatorWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName, $callback)
    {
        /** @var SkribbleService $skribbleService */
        $skribbleService = call_user_func($callback);
        $delegator       = new SkribbleServiceDelegator($skribbleService);

        return $delegator;
    }
}