<?php

namespace IntegrationTest;

use \PHPUnit_Framework_TestCase as TestCase;
use Zend\ServiceManager\ServiceManager;

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
        'ZF\OAuth2\Adapter\MongoAdapter'
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
}
