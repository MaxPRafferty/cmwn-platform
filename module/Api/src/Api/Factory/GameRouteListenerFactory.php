<?php

namespace Api\Factory;

use Api\Listeners\GameRouteListener;
use Game\Service\GameServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class GameRouteListenerFactory
 */
class GameRouteListenerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var GameServiceInterface $gameService */
        $gameService = $serviceLocator->get(GameServiceInterface::class);
        return new GameRouteListener($gameService);
    }
}
