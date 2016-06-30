<?php

namespace Api\V1\Rest\Flip;

use Flip\Service\FlipServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class FlipResourceFactory
 */
class FlipResourceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var FlipServiceInterface $flipService */
        $flipService = $serviceLocator->get(FlipServiceInterface::class);
        return new FlipResource($flipService);
    }
}
