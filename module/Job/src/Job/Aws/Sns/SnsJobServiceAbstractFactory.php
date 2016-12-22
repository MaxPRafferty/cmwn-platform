<?php

namespace Job\Aws\Sns;

use Aws\Sdk;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

/**
 * Class SnsJobServiceAbstractFactory
 */
class SnsJobServiceAbstractFactory implements AbstractFactoryInterface
{
    /**
     * @param ContainerInterface $serviceLocator
     * @param $requestedName
     *
     * @return bool
     */
    protected function getConfig(ContainerInterface $serviceLocator, $requestedName)
    {
        $configKey = strtolower(str_replace('Sns', '', $requestedName) . '-sns-config');
        $config    = $serviceLocator->get('Config');
        if (isset($config[$configKey])) {
            return $config[$configKey];
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return $this->getConfig($container, $requestedName) !== false;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $this->getConfig($container, $requestedName);
        $snsArn = $config['sns-arn'];

        /** @var Sdk $aws */
        $aws       = $container->get(Sdk::class);
        $sqsClient = $aws->createSns(['version' => 'latest', 'region' => 'us-east-1']);

        return new SnsJobService($sqsClient, $snsArn);
    }
}
