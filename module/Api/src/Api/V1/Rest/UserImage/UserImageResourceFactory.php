<?php

namespace Api\V1\Rest\UserImage;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class UserImageResourceFactory
 */
class UserImageResourceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var \Asset\Service\ImageService $imageService */
        $imageService     = $serviceLocator->get('\Asset\Service\ImageService');
        /** @var \Asset\Service\UserImageServiceInterface $userImageService */
        $userImageService = $serviceLocator->get('\Asset\Service\UserImageService');

        return new UserImageResource($imageService, $userImageService);
    }
}
