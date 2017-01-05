<?php

namespace Media\Service;

use Interop\Container\ContainerInterface;
use Zend\Http\Client;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class MediaServiceFactory
 */
class MediaServiceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        // TODO https://www.youtube.com/watch?v=ahKH19iN2SM
        $service = new MediaService($container->get(Client::class));
        $config  = $container->get('Config');

        if (isset($config['media-service']) && isset($config['media-service']['url'])) {
            $service->setBaseUrl($config['media-service']['url']);
        }

        return $service;
    }
}
