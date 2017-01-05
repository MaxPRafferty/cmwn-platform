<?php

namespace Api\V1\Rest\Flip;

use Flip\Service\FlipServiceInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class FlipResourceFactory
 */
class FlipResourceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new FlipResource($container->get(FlipServiceInterface::class));
    }
}
