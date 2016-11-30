<?php

namespace Friend;

use Zend\ModuleManager\Feature\ConfigProviderInterface;

/**
 * Core Classes for Friend
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
}
