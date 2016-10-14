<?php

namespace Notice;

use Zend\EventManager\EventInterface;
use Zend\EventManager\SharedEventManager;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ConsoleBannerProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Console\Adapter\AdapterInterface;
use Zend\Mvc\MvcEvent;

/**
 * Class Module
 * @codeCoverageIgnore
 */
class Module implements
    BootstrapListenerInterface,
    ConfigProviderInterface,
    AutoloaderProviderInterface,
    ConsoleBannerProviderInterface,
    ConsoleUsageProviderInterface
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

    /**
     * Listen to the bootstrap event and attach the listener
     *
     * This is no in the shared-listeners config since we only want to attach these
     * outside the context of testing
     *
     * @param EventInterface $event
     * @return array
     */
    public function onBootstrap(EventInterface $event)
    {
        if (defined('TEST_MODE') && TEST_MODE == true) {
            return;
        }

        /** @var MvcEvent $event */
        /** @var NotifierListener $listener */
        $listener = $event->getApplication()->getServiceManager()->get(NotifierListener::class);
        /** @var SharedEventManager $sharedEvents */
        $sharedEvents = $event->getApplication()->getServiceManager()->get('SharedEventManager');
        $listener->attachShared($sharedEvents);
    }

    /**
     * Returns a string containing a banner text, that describes the module and/or the application.
     * The banner is shown in the console window, when the user supplies invalid command-line parameters or invokes
     * the application with no parameters.
     *
     * The method is called with active Zend\Console\Adapter\AdapterInterface that can be used to directly
     * access Console and send output.
     *
     * @param AdapterInterface $console
     * @return string|null
     */
    public function getConsoleBanner(AdapterInterface $console)
    {
        return "CMWN Cli Jobs for Notice Module";
    }

    /**
     * @param AdapterInterface $console
     * @return array
     */
    public function getConsoleUsage(AdapterInterface $console)
    {
        return [
            'sendmail [--template=(import_success|import_failure|new_user|forgot_password)] [--email=]' =>
                'Helps in testing out the email formats existing in the system'
        ];
    }
}
