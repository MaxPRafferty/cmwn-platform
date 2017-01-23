<?php

namespace Api\Factory;

use Api\Listeners\UserImageListener;
use Asset\Service\UserImageServiceInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class UserImageListenerFactory
 */
class UserImageListenerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new UserImageListener($container->get(UserImageServiceInterface::class));
    }
}
