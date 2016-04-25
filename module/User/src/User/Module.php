<?php

namespace User;

use User\Service\StaticNameService;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\MvcEvent;

/**
 * Core Classes for User
 */
class Module implements ConfigProviderInterface, AutoloaderProviderInterface
{
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
