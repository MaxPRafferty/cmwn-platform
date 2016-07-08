<?php

namespace Api\V1\Rest\Skribble;

use Skribble\Service\SkribbleServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class SkribbleResourceFactory
 */
class SkribbleResourceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var SkribbleServiceInterface $service */
        $service = $serviceLocator->get(SkribbleServiceInterface::class);
        return new SkribbleResource($service);
    }
}
