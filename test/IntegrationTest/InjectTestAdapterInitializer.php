<?php

namespace IntegrationTest;

use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\Pdo\Connection;
use Zend\ServiceManager\Initializer\InitializerInterface;

/**
 * Class InjectTestAdapterListener
 */
class InjectTestAdapterInitializer implements InitializerInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $instance)
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
