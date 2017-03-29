<?php

namespace Search\Service;

use Aws\Credentials\CredentialProvider;
use Aws\Credentials\Credentials;
use Aws\ElasticsearchService\ElasticsearchPhpHandler;
use Elasticsearch\ClientBuilder;
use Elasticsearch\Serializers\ArrayToJSONSerializer;
use Interop\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Search\ElasticHydrator;
use Zend\EventManager\EventManagerInterface;
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
        $config         = $container->get('Config');
        $esConfig       = $config['elastic'] ?? [];
        $awsConfig      = $config['aws'] ?? [];
        $awsCredentials = $awsConfig['credentials'] ?? [];
        $handler        = new ElasticsearchPhpHandler(
            $awsConfig['region'],
            CredentialProvider::fromCredentials(
                new Credentials($awsCredentials['key'], $awsCredentials['secret'])
            )
        );

        $hydrator      = $container->get(ElasticHydrator::class);
        $elasticClient = ClientBuilder::create()
            ->setHosts($esConfig['hosts'] ?? [])
            ->setSerializer(ArrayToJSONSerializer::class)
            ->setLogger($container->get(LoggerInterface::class))
            ->setHandler($handler)
            ->build();

        return new ElasticService(
            $elasticClient,
            $hydrator,
            $esConfig,
            $container->get(EventManagerInterface::class)
        );
    }
}
