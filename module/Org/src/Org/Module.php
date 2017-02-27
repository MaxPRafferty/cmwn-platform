<?php

namespace Org;

use Zend\ModuleManager\Feature\ConfigProviderInterface;

/**
 * Organization module
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
