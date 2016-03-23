<?php

namespace Api;

use Api\Listeners\ImportRouteListener;
use Api\Listeners\ScopeListener;
use Zend\EventManager\SharedEventManager;
use Zend\Mvc\MvcEvent;
use ZF\Apigility\Provider\ApigilityProviderInterface;

/**
 * Class Module
 * @package Api
 */
class Module implements ApigilityProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return [
            'ZF\Apigility\Autoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__,
                ],
            ],
        ];
    }

    public function onBootstrap(MvcEvent $event)
    {
        $app      = $event->getApplication();
        $services = $app->getServiceManager();

        /** @var SharedEventManager $sharedEvents */
        $sharedEvents = $services->get('SharedEventManager');

        /** @var ScopeListener $scope */
        $scope = $services->get('Api\Listeners\ScopeListener');
        $scope->attachShared($sharedEvents);
        
        /** @var ImportRouteListener $import */
        $import = $services->get('Api\Listeners\ImportRouteListener');
        $import->attachShared($sharedEvents);

        $app->getEventManager()->attach($services->get('Api\Listeners\ChangePasswordListener'));
        $app->getEventManager()->attach($services->get('Api\Listeners\UserRouteListener'));
        $app->getEventManager()->attach($services->get('Api\Listeners\GroupRouteListener'));
        
    }
}
