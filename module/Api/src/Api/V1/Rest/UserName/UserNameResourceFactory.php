<?php

namespace Api\V1\Rest\UserName;

use Interop\Container\ContainerInterface;
use User\Service\UserServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class UserNameResourceFactory
 */
class UserNameResourceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new UserNameResource($container->get(UserServiceInterface::class));
    }
}
