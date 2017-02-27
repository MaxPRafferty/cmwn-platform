<?php

namespace Search\Service;

use Elasticsearch\ClientBuilder;
use Elasticsearch\Serializers\ArrayToJSONSerializer;
use Interop\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Search\ElasticHydrator;
use Zend\Config\Config;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Creates the elastic search client
 */
class ElasticServiceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config        = $container->get(Config::class)['elastic'];
        $hydrator      = $container->get(ElasticHydrator::class);
        $elasticClient = ClientBuilder::create()
            ->setHosts($config['hosts'])
            ->setSerializer(ArrayToJSONSerializer::class)
            ->setLogger($container->get(LoggerInterface::class))
            ->build();

        return new ElasticService(
            $elasticClient,
            $hydrator,
            $config
        );
    }
}
