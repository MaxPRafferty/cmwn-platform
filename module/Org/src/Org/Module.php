<?php

namespace Org;

use Zend\ModuleManager\Feature\ConfigProviderInterface;

/**
 * Core Classes for Org
 *
 * @package Org
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
