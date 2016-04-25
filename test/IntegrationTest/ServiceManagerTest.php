<?php

namespace IntegrationTest;

use \PHPUnit_Framework_TestCase as TestCase;
use Zend\File\ClassFileLocator;
use Zend\ServiceManager\ServiceManager;

/**
 * Class ServiceManagerTest
 * @group IntegrationTest
 * @group ServiceManager
 */
class ServiceManagerTest extends TestCase
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * List of services to skip from testing
     * @var array
     */
    protected $blackList = [
        'ZF\OAuth2\Adapter\PdoAdapter',
        'ZF\OAuth2\Adapter\IbmDb2Adapter',
        'ZF\OAuth2\Adapter\MongoAdapter',
        'Zend\Session\SessionManager',
        'Log\App'
    ];

    /**
     * @before
     * @return ServiceManager
     */
    protected function getServiceManager()
    {
        return TestHelper::getServiceManager();
    }

    /**
     * Parses the config to find all services configured in the service manager
     * @return array
     */
    public function servicesProvider()
    {
        $config       = $this->getServiceManager()->get('Config');
        $return       = [];
        $servicesList = [];
        foreach ($config['service_manager'] as $type => $config) {
            if (!in_array($type, ['aliases', 'factories', 'invokables'])) {
                continue;
            }

            $servicesList = array_merge($servicesList, array_keys($config));
        }

        sort($servicesList);
        foreach ($servicesList as $service) {
            if (in_array($service, $this->blackList)) {
                continue;
            }

            $return[$service] = [$service];
        }

        return $return;
    }

    public function applicationClassProvider()
    {
        $iterator = new \GlobIterator(
            realpath(__DIR__ . '/../../module/') . '/**/src/**',
            \FilesystemIterator::KEY_AS_PATHNAME
        );

        $return   = [];
        foreach ($iterator as $path => $file) {
            $locator  = new ClassFileLocator($path);
            foreach ($locator as $classFile) {
                /** @var \Zend\File\PhpClassFile $classFile */
                foreach ($classFile->getClasses() as $class) {

                    if (strpos($class, 'Module') !== false) {
                        continue;
                    }

                    if (strpos($class, 'Api') === 0) {
                        continue;
                    }

                    // Dont need traits
                    if (trait_exists($class)) {
                        continue;
                    }

                    $interfaces = class_implements($class);

                    if (in_array('Zend\ServiceManager\FactoryInterface', $interfaces)) {
                        continue;
                    }

                    if (in_array('Throwable', $interfaces)) {
                        continue;
                    }

                    if (in_array('Zend\ServiceManager\InitializerInterface', $interfaces)) {
                        continue;
                    }

                    if (in_array('Zend\ServiceManager\AbstractFactoryInterface', $interfaces)) {
                        continue;
                    }

                    $return[$class] = [$class];
                }
            }
        }

        return $return;
    }

    /**
     * @param $service
     * @dataProvider servicesProvider
     */
    public function testItShouldBeAbleToLoadService($service)
    {
        try {
            $this->getServiceManager()->get($service);
        } catch (\Exception $serviceException) {
            $previous = $serviceException;
            $prevString = '';
            while (null !== $previous) {
                $prevString .= $previous->getMessage() . PHP_EOL . $previous->getTraceAsString();
                $previous = $previous->getPrevious();
            }

            $this->fail(sprintf(
                'Unable to load service "%s": %s \n%s',
                $service,
                $serviceException->getMessage(),
                $prevString
            ));
        }

        $this->assertTrue(true);
    }

    /**
     * @dataProvider applicationClassProvider
//     */
//    public function testItShouldBeAbleToLoadAllClassesFromServiceManager($class)
//    {
//        $found = false;
//
//        /** @var ControllerManager $controllerManager */
//        $controllerManager = $this->getServiceManager()->get('ControllerManager');
//        if ($controllerManager->has($class)) {
//            $found = true;
//        }
//
//        if ($this->getServiceManager()->has($class)) {
//            $found = true;
//        }
//
//        $this->assertTrue(
//            $found,
//            $class . ' Is Missing From Service Manager'
//        );
//    }
}
