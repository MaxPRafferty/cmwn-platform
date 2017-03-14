<?php

namespace Search;

use Zend\ModuleManager\Feature\ConfigProviderInterface;

/**
 * Search Module
 */
class Module implements ConfigProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }
}
