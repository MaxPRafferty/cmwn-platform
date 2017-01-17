<?php

namespace Api\V1\Rest\Flag;

use Flag\Service\FlagServiceInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class FlagResourceFactory
 */
class FlagResourceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new FlagResource($container->get(FlagServiceInterface::class));
    }
}
