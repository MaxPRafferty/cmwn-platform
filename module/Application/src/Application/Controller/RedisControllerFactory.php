<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\SessionManager;

/**
 * Class RedisControllerFactory
 * @package Application\Controller
 */
class RedisControllerFactory implements FactoryInterface
{
    /**
     * @inheritdoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceLocator = $serviceLocator instanceof ServiceLocatorAwareInterface
            ? $serviceLocator->getServiceLocator()
            : $serviceLocator;
        $sessionManager = $serviceLocator->get(SessionManager::class);
        /**@var \Zend\Session\SaveHandler\Cache $cache*/
        $cache = $sessionManager->getSaveHandler();
        return new RedisController($cache->getCacheStorage());
    }
}
