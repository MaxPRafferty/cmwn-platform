<?php

namespace Api\V1\Rest\Login;

use Interop\Container\ContainerInterface;
use Security\Authentication\AuthAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class LoginResourceFactory
 */
class LoginResourceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new LoginResource(
            $container->get(AuthenticationService::class),
            $container->get(AuthAdapter::class)
        );
    }
}
