<?php

namespace Api;

use Api\Listeners\OrgRouteListener;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\MvcEvent;
use ZF\Apigility\Provider\ApigilityProviderInterface;

/**
 * Class Module
 * @package Api
 */
class Module implements ApigilityProviderInterface, ConfigProviderInterface
{
    /**
     * @return mixed
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    /**
     * @param MvcEvent $event
     */
    public function onBootstrap(MvcEvent $event)
    {
        $app      = $event->getApplication();
        $services = $app->getServiceManager();

        $app->getEventManager()->attach($services->get(OrgRouteListener::class));
    }
}
