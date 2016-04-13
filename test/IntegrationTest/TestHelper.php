<?php

namespace IntegrationTest;

use Zend\Db\Adapter\Adapter;
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
        static::$serviceManager->setAllowOverride(true);
        static::injectTestAdapter();
        return static::$serviceManager;
    }

    /**
     * @return string[]
     */
    public static function getApplicationConfig()
    {
        $appConfig = include __DIR__ . '/../../config/application.config.php';

        $appConfig['module_listener_options']['config_cache_enabled'] = false;
        $appConfig['module_listener_options']['module_map_cache_enabled'] = false;

        return $appConfig;
    }

    public static function injectTestAdapter()
    {
        $adapter = static::getTestDbAdapter();
        static::getServiceManager()->setService('Zend\Db\Adapter\Adapter', $adapter);
    }

    /**
     * @return Adapter
     */
    public static function getTestDbAdapter()
    {
        $phinxConfig = include_once __DIR__ . '/../../config/phinx.php';
        $envConfig   = $phinxConfig['environments']['test'];

        // Map phinx to zf2
        $zf2Config   = [
            'driver'   => 'Pdo',
            'dsn'      => 'mysql:dbname=' . $envConfig['name'] . ';host=' . $envConfig['host'],
            'database' => $envConfig['name'],
            'username' => $envConfig['user'],
            'password' => $envConfig['pass'],
            'driver_options' => [
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
            ],
        ];

        return new Adapter($zf2Config);
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
