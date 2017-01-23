<?php

namespace Api\V1\Rest\SkribbleNotify;

use Interop\Container\ContainerInterface;
use Skribble\Service\SkribbleServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class SkribbleNotifyResourceFactory
 */
class SkribbleNotifyResourceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new SkribbleNotifyResource($container->get(SkribbleServiceInterface::class));
    }
}
