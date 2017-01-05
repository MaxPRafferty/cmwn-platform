<?php

namespace Api\V1\Rest\UserImage;

use Asset\Service\ImageServiceInterface;
use Asset\Service\UserImageServiceInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class UserImageResourceFactory
 */
class UserImageResourceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new UserImageResource(
            $container->get(ImageServiceInterface::class),
            $container->get(UserImageServiceInterface::class)
        );
    }
}
