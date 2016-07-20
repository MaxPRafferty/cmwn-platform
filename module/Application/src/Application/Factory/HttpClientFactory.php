<?php

namespace Application\Factory;

use Zend\Http\Client as HttpClient;
use Zend\Http\Client\Adapter\AdapterInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class HttpClientFactory
 */
class HttpClientFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $httpConfig = isset($config['http-config']) ? $config['http-config'] : [];

        return new HttpClient(null, $httpConfig);
    }
}
