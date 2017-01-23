<?php

namespace Security;

use Zend\Console\Adapter\AdapterInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ConsoleBannerProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;

/**
 * Security Module
 */
class Module implements
    ConfigProviderInterface,
    ConsoleBannerProviderInterface,
    ConsoleUsageProviderInterface
{
    /**
     * @inheritdoc
     */
    public function getConsoleBanner(AdapterInterface $console)
    {
        return 'CMWN Security Control';
    }

    /**
     * @inheritdoc
     */
    public function getConsoleUsage(AdapterInterface $console)
    {
        return [
            'create:user'    => 'Creates a new super admin',
            'security:perms' => 'Creates a dump of the current permissions',
        ];
    }

    /**
     * @inheritdoc
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }
}
