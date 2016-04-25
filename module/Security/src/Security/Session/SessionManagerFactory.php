<?php

namespace Security\Session;

use Zend\Cache\StorageFactory;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\SaveHandler\Cache;
use Zend\Session\SessionManager;
use Zend\Session\Container;

/**
 * Class SessionManagerFactory
 * @package Security\Factory
 * @codeCoverageIgnore
 */
class SessionManagerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $service
     * @return SessionManager
     * @todo break up
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function createService(ServiceLocatorInterface $service)
    {
        $config = $service->get('config');
        if (!isset($config['session'])) {
            $sessionManager = new SessionManager();
            Container::setDefaultManager($sessionManager);
            return $sessionManager;
        }

        $session = $config['session'];

        $sessionConfig = null;
        if (isset($session['config'])) {
            $class         = isset($session['config']['class'])
                ? $session['config']['class']
                : 'Zend\Session\Config\SessionConfig';

            $options       = isset($session['config']['options']) ? $session['config']['options'] : [];
            /** @var \Zend\Session\Config\SessionConfig $sessionConfig */
            $sessionConfig = new $class();
            $sessionConfig->setOptions($options);
        }

        $sessionStorage = null;
        if (isset($session['storage'])) {
            $class          = $session['storage'];
            $sessionStorage = new $class();
        }

        $sessionSaveHandler = null;
        if (isset($session['save_handler'])) {
            // class should be fetched from service manager since it will require constructor arguments
            $sessionSaveHandler = new Cache(StorageFactory::factory($session['save_handler']));
        }

        $sessionManager = new SessionManager($sessionConfig, $sessionStorage, $sessionSaveHandler);

        Container::setDefaultManager($sessionManager);
        return $sessionManager;
    }
}
