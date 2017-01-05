<?php

namespace Job\Aws\Sqs;

use Aws\Sdk;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\AbstractFactoryInterface;

/**
 * Class SqsJobServiceAbstractFactory
 */
class SqsJobServiceAbstractFactory implements AbstractFactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param $requestedName
     *
     * @return bool
     */
    protected function getConfig(ContainerInterface $container, $requestedName)
    {
        $configKey = strtolower(str_replace('Sqs', '', $requestedName) . '-sqs-config');
        $config    = $container->get('Config');
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
        $config   = $this->getConfig($container, $requestedName);
        $queueUrl = $config['queue-url'];

        /** @var Sdk $aws */
        $aws       = $container->get(Sdk::class);
        $sqsClient = $aws->createSqs(['version' => 'latest']);

        return new SqsJobService($sqsClient, $queueUrl);
    }
}
