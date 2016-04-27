<?php

namespace Api\Factory;

use Api\Listeners\UserImageListener;
use Asset\Service\UserImageServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class UserImageListenerFactory
 */
class UserImageListenerFactory implements FactoryInterface
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
        /** @var \Asset\Service\UserImageServiceInterface $userImageService */
        $userImageService = $serviceLocator->get(UserImageServiceInterface::class);
        return new UserImageListener($userImageService);
    }
}
