<?php

namespace Api\V1\Rest\Logout;

use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class LogoutResourceFactory
 */
class LogoutResourceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new LogoutResource($container->get(AuthenticationService::class));
    }
}
