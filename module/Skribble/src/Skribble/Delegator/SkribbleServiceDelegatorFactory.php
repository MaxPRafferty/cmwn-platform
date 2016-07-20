<?php

namespace Skribble\Delegator;

use Skribble\Service\SkribbleService;
use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class SkribbleServiceDelegatorFactory
 */
class SkribbleServiceDelegatorFactory implements DelegatorFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function createDelegatorWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName, $callback)
    {
        /** @var SkribbleService $userService */
        $skribbleService = call_user_func($callback);
        $delegator       = new SkribbleServiceDelegator($skribbleService);

        return $delegator;
    }

}
