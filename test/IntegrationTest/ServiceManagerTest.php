<?php

namespace IntegrationTest;

use PHPUnit\Framework\TestCase as TestCase;
use Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * Class ServiceManagerTest
 *
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
     *
     * Modules may add services that are never invoked.  This test will try
     * load all services that we may not have configured to use
     *
     * @var array
     */
    protected $blackList = [
        \ZF\OAuth2\Adapter\PdoAdapter::class,
        \ZF\OAuth2\Adapter\IbmDb2Adapter::class,
        \ZF\OAuth2\Adapter\MongoAdapter::class,
        \Zend\Session\SessionManager::class,
        'Log\App',
        \ZF\Configuration\ConfigResource::class,
        \AwsModule\Session\SaveHandler\DynamoDb::class,
        'mailviewrenderer',
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
     *
     * @return array
     */
    public function servicesProvider()
    {
        $config       = $this->getServiceManager()->get('Config');
        $return       = [];
        $servicesList = [];
        // Get all everything from the service manager
        foreach ($config['service_manager'] as $type => $serviceConfig) {
            if (!in_array($type, ['aliases', 'factories', 'invokables', 'services'])) {
                continue;
            }

            $servicesList = array_merge($servicesList, array_keys($serviceConfig));
        }

        // Gets everything from the config abstract factory
        foreach (array_keys($config[ConfigAbstractFactory::class]) as $service) {
            array_push($servicesList, $service);
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

    /**
     * @param $serviceName
     *
     * @dataProvider servicesProvider
     */
    public function testItShouldBeAbleToLoadService($serviceName)
    {
        try {
            $service = $this->getServiceManager()->get($serviceName);
        } catch (\Exception $serviceException) {
            $previous   = $serviceException;
            $prevString = '';
            while (null !== $previous) {
                $prevString .= $previous->getMessage() . PHP_EOL . $previous->getTraceAsString();
                $previous = $previous->getPrevious();
            }

            $this->fail(sprintf(
                'Unable to load service "%s": %s \n%s',
                $serviceName,
                $serviceException->getMessage(),
                $prevString
            ));

            return;
        }

        $this->assertNotNull($service);
        $this->assertTrue(true);
    }
}
