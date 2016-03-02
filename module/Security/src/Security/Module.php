<?php

namespace Security;

use Security\Guard\CsrfGuard;
use Security\Guard\OriginGuard;
use Zend\Console\Request;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;

/**
 * Core Classes for Cmwn
 *
 * @package Cmwn
 * @codeCoverageIgnore
 */
class Module implements ConfigProviderInterface, AutoloaderProviderInterface
{
    /**
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    /**
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__,
                ],
            ],
        ];
    }

    public function onBootstrap(MvcEvent $event)
    {
        if ($event->getRequest() instanceof Request) {
            return;
        }

        /** @var \Zend\Session\SessionManager $session */
        $session = $event->getApplication()
            ->getServiceManager()
            ->get('Zend\Session\SessionManager');
        $session->start();

        $container = new Container('initialized');

        // This sets the user agennt and remote addr on the 1st request to the application

        if (!isset($container->init)) {
            $serviceManager = $event->getApplication()->getServiceManager();
            $request        = $serviceManager->get('Request');

            $session->regenerateId(true);
            $container->init          = 1;
            $container->remoteAddr    = $request->getServer()->get('REMOTE_ADDR');
            $container->httpUserAgent = $request->getServer()->get('HTTP_USER_AGENT');

            $config = $serviceManager->get('Config');
            if (!isset($config['session'])) {
                return;
            }

            $sessionConfig = $config['session'];
            if (isset($sessionConfig['validators'])) {
                $chain   = $session->getValidatorChain();

                foreach ($sessionConfig['validators'] as $validator) {
                    switch ($validator) {
                        case 'Zend\Session\Validator\HttpUserAgent':
                            $validator = new $validator($container->httpUserAgent);
                            break;
                        case 'Zend\Session\Validator\RemoteAddr':
                            $validator  = new $validator($container->remoteAddr);
                            break;
                        default:
                            $validator = new $validator();
                    }

                    $chain->attach('session.validate', array($validator, 'isValid'));
                }
            }
        }

        // Attaches the origin header check to ensure this is not coming from another origin

        $app          = $event->getTarget();
        $events       = $app->getEventManager();

        $events->attachAggregate(new OriginGuard());
        $events->attachAggregate(new CsrfGuard(['session' => $container]));
    }
}
