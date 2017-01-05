<?php

namespace Application\Factory;

use Interop\Container\ContainerInterface;
use Zend\Http\Client as HttpClient;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class HttpClientFactory
 */
class HttpClientFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config     = $container->get('Config');
        $httpConfig = isset($config['http-config']) ? $config['http-config'] : [];

        return new HttpClient(null, $httpConfig);
    }
}
