<?php

namespace Sa;

use Zend\ModuleManager\Feature\ConfigProviderInterface;

/**
 * Class Module
 * @package Sa
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
