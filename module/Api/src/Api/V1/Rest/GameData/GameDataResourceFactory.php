<?php

namespace Api\V1\Rest\GameData;

use Game\Service\SaveGameServiceInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class GameDataResourceFactory
 */
class GameDataResourceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new GameDataResource($container->get(SaveGameServiceInterface::class));
    }
}
