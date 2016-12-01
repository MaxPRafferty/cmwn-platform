<?php

namespace Suggest;

use Zend\Console\Adapter\AdapterInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ConsoleBannerProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;

/**
 * Core Classes for Suggest
 */
class Module implements
    ConfigProviderInterface,
    ConsoleBannerProviderInterface,
    ConsoleUsageProviderInterface
{
    /**
     * @return mixed
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    /**
     * Returns a string containing a banner text, that describes the module and/or the application.
     * The banner is shown in the console window, when the user supplies invalid command-line parameters or invokes
     * the application with no parameters.
     *
     * The method is called with active Zend\Console\Adapter\AdapterInterface that can be used to directly
     * access Console and send output.
     *
     * @param AdapterInterface $console
     * @return string|null
     */
    public function getConsoleBanner(AdapterInterface $console)
    {
        return "CMWN Cli Jobs for Suggested Friends Engine";
    }

    /**
     * @param AdapterInterface $console
     * @return array
     */
    public function getConsoleUsage(AdapterInterface $console)
    {
        return [
            'suggest [--user_id=]'      => 'Runs the suggested friends engine for a given user',
            'cron:suggest' => 'Runs the suggested friends engine for the users existing in the database'
        ];
    }
}
