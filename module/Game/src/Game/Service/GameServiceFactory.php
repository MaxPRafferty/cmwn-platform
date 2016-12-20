<?php

namespace Game\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class GameServiceFactory
 * @package Game\Service
 * @codeCoverageIgnore
 */
class GameServiceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new GameService($container->get('GamesTable'));
    }
}
