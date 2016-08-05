<?php
namespace Api\V1\Rest\Media;

use Media\Service\MediaServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class MediaResourceFactory
 */
class MediaResourceFactory implements FactoryInterface
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
        /** @var MediaServiceInterface $mediaService */
        $mediaService = $serviceLocator->get(MediaServiceInterface::class);
        return new MediaResource($mediaService);
    }
}
