<?php

namespace IntegrationTest;

use Zend\Db\Adapter\Adapter;
use Zend\Mvc\MvcEvent;
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

        static::$serviceManager = Application::init(static::getApplicationConfig())->getServiceManager();
        return static::$serviceManager;
    }

    public static function getTestDbConfig()
    {
        $phinxConfig = include __DIR__ . '/../../config/phinx.php';
        $envConfig   = $phinxConfig['environments']['test'];

        // Map phinx to zf2
        return [
            'driver'   => 'Pdo',
            'dsn'      => 'mysql:dbname=' . $envConfig['name'] . ';host=' . $envConfig['host'],
            'database' => $envConfig['name'],
            'username' => $envConfig['user'],
            'password' => $envConfig['pass'],
            'driver_options' => [
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
            ],
        ];
    }
    /**
     * @return string[]
     */
    public static function getApplicationConfig()
    {
        $appConfig = include __DIR__ . '/../../config/application.config.php';

        $appConfig['module_listener_options']['config_cache_enabled'] = false;
        $appConfig['module_listener_options']['module_map_cache_enabled'] = false;

        $appConfig['service_manager'] = [
            'invokables' => [
                'InjectTestAdapter' => InjectTestAdapterListener::class
            ]
        ];

        $appConfig['listeners'] = ['InjectTestAdapter'];
        return $appConfig;
    }

    /**
     * @return array|object
     */
    public static function getConfig()
    {
        return static::getServiceManager()->get('Config');
    }

    /**
     * @param $key
     * @return string[]
     */
    public static function getConfigKey($key)
    {
        $config = static::getConfig();
        return isset($config[$key]) ? $config[$key] : [];
    }

    /**
     * @return string[]
     */
    public static function getRoutes()
    {
        $routerConfig = static::getConfigKey('router');
        $routes       = $routerConfig['routes'];

        unset($routes['zf-apigility']);
        return $routes;
    }
}
