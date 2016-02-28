<?php

namespace IntegrationTest;

use ZF\Apigility\Application;
use Zend\ServiceManager\ServiceManager;

/**
 * TestHelper
 *
 * @author Chuck "MANCHUCK" Reeves <chuck@manchuck.com>
 * @codeCoverageIgnore
 */
class TestHelper
{
    /**
     * @var ServiceManager
     */
    protected static $serviceManager;

    /**
     * @return bool
     */
    public static function isBootstrapped()
    {
        return null !== static::$serviceManager;
    }

    /**
     * @before
     * @return ServiceManager
     */
    public static function getServiceManager()
    {
        if (null !== static::$serviceManager) {
            return static::$serviceManager;
        }

        $appConfig = include '../config/application.config.php';

        $appConfig['module_listener_options']['config_cache_enabled'] = false;
        $appConfig['module_listener_options']['module_map_cache_enabled'] = false;

        static::$serviceManager = Application::init($appConfig)->getServiceManager();

        return static::$serviceManager;
    }
}
