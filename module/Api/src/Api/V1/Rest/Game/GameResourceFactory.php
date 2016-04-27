<?php

namespace Api\V1\Rest\Game;

use Game\Service\GameServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class GameResourceFactory
 * @package Api\V1\Rest\Game
 */
class GameResourceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var GameServiceInterface $gameService */
        $gameService = $serviceLocator->get(GameServiceInterface::class);
        return new GameResource($gameService);
    }
}
