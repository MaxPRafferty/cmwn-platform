<?php

namespace Api\V1\Rest\SaveGame;

use Game\Service\SaveGameServiceInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class SaveGameResourceFactory
 */
class SaveGameResourceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new SaveGameResource($container->get(SaveGameServiceInterface::class));
    }
}
