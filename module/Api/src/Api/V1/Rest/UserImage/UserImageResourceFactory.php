<?php

namespace Api\V1\Rest\UserImage;

use Asset\Service\ImageServiceInterface;
use Asset\Service\UserImageServiceInterface;
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
        /** @var ImageServiceInterface $imageService */
        /** @var UserImageServiceInterface $userImageService */
        $imageService     = $serviceLocator->get(ImageServiceInterface::class);
        $userImageService = $serviceLocator->get(UserImageServiceInterface::class);

        return new UserImageResource($imageService, $userImageService);
    }
}
