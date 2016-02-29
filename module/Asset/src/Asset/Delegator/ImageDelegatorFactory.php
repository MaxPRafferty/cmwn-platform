<?php

namespace Asset\Delegator;

use Asset\Service\ImageService;
use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ImageDelegatorFactory
 * @package Asset\Delegator
 * @codeCoverageIgnore
 */
class ImageDelegatorFactory implements DelegatorFactoryInterface
{
    /**
     * A factory that creates delegates of a given service
     *
     * @param ServiceLocatorInterface $serviceLocator the service locator which requested the service
     * @param string $name the normalized service name
     * @param string $requestedName the requested service name
     * @param callable $callback the callback that is responsible for creating the service
     *
     * @return mixed
     */
    public function createDelegatorWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName, $callback)
    {
        /** @var ImageService $imageService */
        $imageService = call_user_func($callback);
        $delegator   = new ImageServiceDelegator($imageService);
        return $delegator;
    }
}
