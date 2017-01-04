<?php

namespace IntegrationTest;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\Pdo\Pdo;
use Zend\Log\Logger;
use Zend\Log\Writer\Noop;
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
     * @var ServiceManager
     */
    protected static $dbServiceManager;

    /**
     * @var \PDO
     */
    protected static $pdo;

    /**
     * @var array
     */
    protected static $appConfig;

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

        $testLogger = new Logger();
        $testLogger->addWriter(new Noop());

        static::$serviceManager->setService('Log\App', $testLogger);
        static::$serviceManager->setAllowOverride(false);

        return static::$serviceManager;
    }

    /**
     * @before
     * @return ServiceManager
     */
    public static function getDbServiceManager()
    {
        if (null !== static::$dbServiceManager) {
            return static::$dbServiceManager;
        }

        static::$dbServiceManager = Application::init(static::getApplicationConfigWithDb())->getServiceManager();
        static::$dbServiceManager->setAllowOverride(true);

        $testLogger = new Logger();
        $testLogger->addWriter(new Noop());

        static::$dbServiceManager->setService('Log\App', $testLogger);
        static::$dbServiceManager->setAllowOverride(false);

        return static::$dbServiceManager;
    }

    /**
     * @return array
     */
    public static function getTestDbConfig()
    {
        $phinxConfig = include __DIR__ . '/../../config/phinx.php';
        $envConfig   = $phinxConfig['environments']['test'];

        // Map phinx to zf2
        return [
            'driver'         => 'Pdo',
            'dsn'            => 'mysql:dbname=' . $envConfig['name'] . ';host=' . $envConfig['host'],
            'database'       => $envConfig['name'],
            'username'       => $envConfig['user'],
            'password'       => $envConfig['pass'],
            'driver_options' => [
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',
            ],
        ];
    }

    /**
     * @return string[]
     */
    public static function getApplicationConfig()
    {
        if (static::$appConfig === null) {
            static::$appConfig = include __DIR__ . '/../../config/application.config.php';

            static::$appConfig['module_listener_options']['config_cache_enabled']     = true;
            static::$appConfig['module_listener_options']['module_map_cache_enabled'] = true;

            $cacheFile = 'module-config-cache.'
                . static::$appConfig['module_listener_options']['config_cache_key']
                . '.php';

            static::$appConfig['service_manager']['services'][Adapter::class] = new Adapter(
                new Pdo(
                    static::getPdoConnection()
                )
            );

            @unlink(static::$appConfig['module_listener_options']['cache_dir'] . '/' . $cacheFile);
        }

        return static::$appConfig;
    }

    /**
     * @return string[]
     * @deprecated
     */
    public static function getApplicationConfigWithDb()
    {
        return static::getApplicationConfig();
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
     *
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

    /**
     * @return \PDO
     */
    public static function getPdoConnection()
    {
        if (static::$pdo === null) {
            $config      = static::getTestDbConfig();
            static::$pdo = new \PDO(
                $config['dsn'],
                $config['username'],
                $config['password'],
                $config['driver_options']
            );
        }

        return static::$pdo;
    }
}
