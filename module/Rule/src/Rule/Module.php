<?php

namespace Rule;

use Rule\Engine\Engine;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\MvcEvent;

/**
 * Module for Rules
 *
 * @codeCoverageIgnore
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
     * @inheritDoc
     */
    public function onBootstrap(MvcEvent $mvcEvent)
    {
        $mvcEvent->getApplication()->getServiceManager()->get(Engine::class);
    }
}
