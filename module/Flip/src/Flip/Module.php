<?php

namespace Flip;

use Zend\ModuleManager\Feature\ConfigProviderInterface;

/**
 * The Flips Module
 */
class Module implements ConfigProviderInterface
{
    /**
     * @inheritdoc
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }
}
