<?php

namespace Api\V1\Rest\SkribbleNotify;

use Skribble\Service\SkribbleServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class SkribbleNotifyResourceFactory
 */
class SkribbleNotifyResourceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var SkribbleServiceInterface $skribbleService */
        $skribbleService = $serviceLocator->get(SkribbleServiceInterface::class);
        return new SkribbleNotifyResource($skribbleService);
    }
}
