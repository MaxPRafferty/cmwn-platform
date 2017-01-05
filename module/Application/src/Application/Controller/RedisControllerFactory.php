<?php

namespace Application\Controller;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Session\SessionManager;

/**
 * Class RedisControllerFactory
 */
class RedisControllerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var \Zend\Session\SaveHandler\Cache $cache */
        $sessionManager = $container->get(SessionManager::class);
        $cache          = $sessionManager->getSaveHandler();

        return new RedisController($cache->getCacheStorage());
    }
}
