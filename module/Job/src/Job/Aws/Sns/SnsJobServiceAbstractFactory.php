<?php

namespace Job\Aws\Sns;

use Aws\Sdk;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class SnsJobServiceAbstractFactory
 */
class SnsJobServiceAbstractFactory implements AbstractFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config = $this->getConfig($serviceLocator, $requestedName);

        return $config !== false;
    }

    /**
     * @inheritDoc
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config = $this->getConfig($serviceLocator, $requestedName);
        $snsArn = $config['sns-arn'];

        /** @var Sdk $aws */
        $aws       = $serviceLocator->get(Sdk::class);
        $sqsClient = $aws->createSns(['version' => 'latest']);

        return new SnsJobService($sqsClient, $snsArn);
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param $requestedName
     *
     * @return bool
     */
    protected function getConfig(ServiceLocatorInterface $serviceLocator, $requestedName)
    {
        $configKey = strtolower(str_replace('Sns', '', $requestedName) . '-sns-config');
        $config    = $serviceLocator->get('Config');
        if (isset($config[$configKey])) {
            return $config[$configKey];
        }

        return false;
    }
}
