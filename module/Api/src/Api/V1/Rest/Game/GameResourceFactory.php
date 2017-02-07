<?php

namespace Api\V1\Rest\Game;

use Game\Service\GameServiceInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class GameResourceFactory
 */
class GameResourceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new GameResource($container->get(GameServiceInterface::class));
    }
}
