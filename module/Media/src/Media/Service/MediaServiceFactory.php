<?php

namespace Media\Service;

use Zend\Http\Client;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class MediaServiceFactory
 */
class MediaServiceFactory implements FactoryInterface
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
        /** @var Client $httpClient */
        $httpClient = $serviceLocator->get(Client::class);
        return new MediaService($httpClient);
    }
}
