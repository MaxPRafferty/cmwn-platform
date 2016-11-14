<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace Application;

use Application\Listeners\ListenersAggregate;
use Application\Utils\StaticType;
use Zend\Console\Adapter\AdapterInterface;
use Zend\EventManager\SharedEventManager;
use Zend\Log\Logger;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ConsoleBannerProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

/**
 * Class Module
 *
 * @package Application
 * @codeCoverageIgnore
 */
class Module implements
    ConfigProviderInterface,
    ConsoleUsageProviderInterface
{
    /**
     * @param MvcEvent $mvcEvent
     */
    public function onBootstrap(MvcEvent $mvcEvent)
    {
        $eventManager        = $mvcEvent->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        if (!defined('TEST_MODE')) {
            $logger = $mvcEvent->getApplication()->getServiceManager()->get('Log\App');
            Logger::registerErrorHandler($logger);
            Logger::registerExceptionHandler($logger);
            Logger::registerFatalErrorShutdownFunction($logger);
        }

        $this->attachShared($mvcEvent);

        $config = $mvcEvent->getApplication()->getServiceManager()->get('Config');
        StaticType::setTypes(isset($config['cmwn-types']) ? $config['cmwn-types'] : []);
    }

    /**
     * @return mixed
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
     * Attaches Shared listeners
     *
     * @param MvcEvent $event
     */
    protected function attachShared(MvcEvent $event)
    {
        /** @var \Application\Listeners\ListenersAggregate $aggregate */
        /** @var SharedEventManager $sharedEvents */
        $service      = $event->getApplication()->getServiceManager();
        $aggregate    = $service->get(ListenersAggregate::class);
        $sharedEvents = $service->get('SharedEventManager');

        $aggregate->attachShared($sharedEvents);
    }

    /**
     * @inheritdoc
     */
    public function getConsoleUsage(AdapterInterface $console)
    {
        return [
            'redis:delete'   => 'delete cache',
        ];
    }
}
