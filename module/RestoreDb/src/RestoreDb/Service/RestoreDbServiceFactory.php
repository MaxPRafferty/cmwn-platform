<?php

namespace RestoreDb\Service;

use Zend\Db\Adapter\Adapter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class RestoreDbServiceFactory
 * @package RestoreDb\Service
 */
class RestoreDbServiceFactory implements FactoryInterface
{
    /**
     * @inheritdoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config');
        $testData = isset($config['test-data']) ? $config['test-data'] : [];
        $adapter = $serviceLocator->get(Adapter::class);
        $realService = new RestoreDbService($testData, $adapter);
        return $realService;
    }
}
