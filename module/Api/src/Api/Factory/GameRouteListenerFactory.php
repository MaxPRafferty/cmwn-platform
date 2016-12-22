<?php

namespace Api\Factory;

use Api\Listeners\GameRouteListener;
use Game\Service\GameServiceInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class GameRouteListenerFactory
 */
class GameRouteListenerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new GameRouteListener($container->get(GameServiceInterface::class));
    }
}
