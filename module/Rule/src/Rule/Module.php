<?php

namespace Rule;

use Zend\ModuleManager\Feature\ConfigProviderInterface;

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
}
