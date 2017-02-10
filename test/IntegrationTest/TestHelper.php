<?php

namespace IntegrationTest;

use Search\Service\ElasticServiceInterface;
use Zend\Authentication\Storage\NonPersistent;
use Zend\Authentication\Storage\StorageInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\Pdo\Pdo;
use ZF\Apigility\Application;
use Zend\ServiceManager\ServiceManager;
use PHPUnit\DbUnit\Database\DefaultConnection as TestConnection;

/**
 * TestHelper
 */
class TestHelper
{
    /**
     * @var ServiceManager
     */
    protected static $serviceManager;

    /**
     * @var \Mockery\MockInterface|ElasticServiceInterface
     */
    protected static $elasticMock;

    /**
     * @var \PDO
     */
    protected static $pdo;

    /**
     * @var array
     */
    protected static $appConfig;

    /**
     * @var TestConnection
     */
    protected static $testConn;

    /**
     * @var Adapter
     */
    protected static $dbAdapter;

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
                \PDO::ATTR_PERSISTENT         => true,
            ],
        ];
    }

    /**
     * @return string[]
     */
    public static function getApplicationConfig()
    {
        if (static::$appConfig === null) {
            static::$appConfig = [
                'modules'                 => include __DIR__ . '/../../config/modules.config.php',
                'module_listener_options' => [
                    'module_paths'             => [
                        './module',
                        './vendor',
                    ],
                    'config_glob_paths'        => [
                        __DIR__ . '/../../config/autoload/{,*.}{global,local}.php',
                        __DIR__ . '/../../config/games/{games}{*}.php',
                        __DIR__ . '/../../config/rules/{*.}rule.php',
                    ],
                    'config_cache_key'         => 'test.config.cache',
                    'module_map_cache_key'     => 'test.module.cache',
                    'cache_dir'                => realpath(__DIR__ . '/../../data/cache/'),
                    'module_map_cache_enabled' => true,
                    'config_cache_enabled'     => true,
                ],
                'service_manager'         => [
                    'services' => [
                        Adapter::class                 => static::getDbAdapter(),
                        StorageInterface::class        => new NonPersistent(),
                        ElasticServiceInterface::class => static::getElasticMock(),
                    ],
                ],
            ];
        }

        return static::$appConfig;
    }

    /**
     * @return Adapter
     */
    public static function getDbAdapter(): Adapter
    {
        if (static::$dbAdapter == null) {
            static::$dbAdapter = new Adapter(new Pdo(static::getPdoConnection()));
        }

        return static::$dbAdapter;
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

    /**
     * @return TestConnection
     */
    public static function getTestConnection(): TestConnection
    {
        if (static::$testConn === null) {
            static::$testConn = new TestConnection(
                TestHelper::getPdoConnection(),
                static::getTestDbConfig()['database']
            );
        }

        return static::$testConn;
    }

    /**
     * @return \Mockery\MockInterface|ElasticServiceInterface
     */
    public static function getElasticMock()
    {
        if (static::$elasticMock !== null) {
            return static::$elasticMock;
        }

        // We cannot connect to elastic outside the office IP Block
        // To avoid the rules engine mucking up elastic search we just prevent elastic
        // from doing anything with a mock

        /** @var \Mockery\MockInterface|ElasticServiceInterface $elasticService */
        static::$elasticMock = \Mockery::mock(ElasticServiceInterface::class);
        $reflect             = new \ReflectionClass(ElasticServiceInterface::class);
        $methods             = $reflect->getMethods();
        array_walk($methods, function (\ReflectionMethod $method) {
            static::$elasticMock->shouldReceive($method->getName())->byDefault();
        });

        return static::$elasticMock;
    }
}
