<?php

namespace Api;

use Api\Listeners\HalListenersAggregate;
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
        $aggregate = new HalListenersAggregate();
        $aggregate->attachShared($sharedEvents);

        $app->getEventManager()->attach($services->get('Api\Listeners\ChangePasswordListener'));
        $app->getEventManager()->attach($services->get('Api\Listeners\UserRouteListener'));
    }
}
