<?php

namespace User;

use User\Service\StaticNameService;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\MvcEvent;

/**
 * Core Classes for User
 */
class Module implements ConfigProviderInterface
{
    /**
     * @return mixed
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    /**
     * Seeds the approved username into the StaticNameService
     *
     * @param MvcEvent $event
     */
    public function onBootstrap(MvcEvent $event)
    {
        $appConfig = $event->getTarget()->getServiceManager()->get('Config');
        StaticNameService::seedNames($appConfig['user-names']);
    }
}
