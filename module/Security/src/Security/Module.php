<?php

namespace Security;

use Zend\Console\Adapter\AdapterInterface;
use Zend\Console\Request as ConsoleRequest;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ConsoleBannerProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;

/**
 * Core Classes for Cmwn
 *
 * @package Cmwn
 * @codeCoverageIgnore
 */
class Module implements
    ConfigProviderInterface,
    AutoloaderProviderInterface,
    ConsoleBannerProviderInterface,
    ConsoleUsageProviderInterface
{
    /**
     * Returns a string containing a banner text, that describes the module and/or the application.
     * The banner is shown in the console window, when the user supplies invalid command-line parameters or invokes
     * the application with no parameters.
     *
     * The method is called with active Zend\Console\Adapter\AdapterInterface that can be used to directly access Console and send
     * output.
     *
     * @param AdapterInterface $console
     * @return string|null
     */
    public function getConsoleBanner(AdapterInterface $console)
    {
        return 'CMWN Super Admin Control';
    }

    /**
     * Returns an array or a string containing usage information for this module's Console commands.
     * The method is called with active Zend\Console\Adapter\AdapterInterface that can be used to directly access
     * Console and send output.
     *
     * If the result is a string it will be shown directly in the console window.
     * If the result is an array, its contents will be formatted to console window width. The array must
     * have the following format:
     *
     *     return array(
     *                'Usage information line that should be shown as-is',
     *                'Another line of usage info',
     *
     *                '--parameter'        =>   'A short description of that parameter',
     *                '-another-parameter' =>   'A short description of another parameter',
     *                ...
     *            )
     *
     * @param AdapterInterface $console
     * @return array|string|null
     */
    public function getConsoleUsage(AdapterInterface $console)
    {
        return [
            'create user' => 'Creates a new'
        ];
    }


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

    /**
     * Registers the session and attaches guards
     *
     * @param MvcEvent $event
     */
    public function onBootstrap(MvcEvent $event)
    {
        if ($event->getRequest() instanceof ConsoleRequest) {
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

                    $chain->attach('session.validate', [$validator, 'isValid']);
                }
            }
        }

        $this->attachGuards($event);
    }

    /**
     * Attaches all the guards for the routes
     *
     * @todo Make one aggreate that can be configured
     * @param MvcEvent $event
     */
    protected function attachGuards(MvcEvent $event)
    {
        $events  = $event->getApplication()->getEventManager();
        $service = $event->getApplication()->getServiceManager();

        $events->attach($service->get('Security\Guard\OriginGuard'));
        $events->attach($service->get('Security\Guard\CsrfGuard'));
        $events->attach($service->get('Security\Guard\ResetPasswordGuard'));
    }
}
