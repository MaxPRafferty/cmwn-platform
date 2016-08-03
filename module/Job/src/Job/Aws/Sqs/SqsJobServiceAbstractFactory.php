<?php

namespace Job\Aws\Sqs;

use Aws\Sdk;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class SqsJobServiceAbstractFactory
 */
class SqsJobServiceAbstractFactory implements AbstractFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config   = $this->getConfig($serviceLocator, $requestedName);
        return $config !== false;
    }

    /**
     * @inheritDoc
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config   = $this->getConfig($serviceLocator, $requestedName);
        $queueUrl = $config['queue-url'];

        /** @var Sdk $aws */
        $aws       = $serviceLocator->get(Sdk::class);
        $sqsClient = $aws->createSqs(['version' => 'latest']);

        return new SqsJobService($sqsClient, $queueUrl);
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param $requestedName
     *
     * @return bool
     */
    protected function getConfig(ServiceLocatorInterface $serviceLocator, $requestedName)
    {
        $configKey = strtolower(str_replace('Sqs', '', $requestedName) . '-sqs-config');
        $config    = $serviceLocator->get('Config');
        if (isset($config[$configKey])) {
            return $config[$configKey];
        }

        return false;
    }
}
