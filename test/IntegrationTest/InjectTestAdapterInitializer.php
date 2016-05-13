<?php

namespace IntegrationTest;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\Pdo\Connection;
use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class InjectTestAdapterListener
 */
class InjectTestAdapterInitializer implements InitializerInterface
{
    /**
     * Initialize
     *
     * @param $instance
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        if (!$instance instanceof Adapter) {
            return;
        }
        
        $connection = $instance->getDriver()->getConnection();
        if (!$connection instanceof Connection) {
            return;
        }

        $connection->setResource(TestHelper::getPdoConnection());
    }
}
