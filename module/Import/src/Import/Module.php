<?php

namespace Import;

use Zend\ModuleManager\Feature\ConfigProviderInterface;

/**
 * Core Classes for Cmwn
 *
 * @package Cmwn
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
