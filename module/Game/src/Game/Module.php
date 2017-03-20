<?php

namespace Game;

use Zend\ModuleManager\Feature\ConfigProviderInterface;

/**
 * Game Module
 */
class Module implements ConfigProviderInterface
{
    /**
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }
}
