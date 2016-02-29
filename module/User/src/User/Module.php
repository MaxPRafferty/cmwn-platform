<?php

namespace User;

use User\Service\StaticNameService;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\MvcEvent;

/**
 * Core Classes for Cmwn
 *
 * @package Cmwn
 * @codeCoverageIgnore
 */
class Module implements ConfigProviderInterface
{
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
        $appConfig = $event->getTarget()->getServiceManager()->get('Config');
        StaticNameService::seedNames($appConfig['user-names']);
    }
}
