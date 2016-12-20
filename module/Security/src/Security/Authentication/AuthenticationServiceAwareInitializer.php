<?php

namespace Security\Authentication;

use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\ServiceManager\Initializer\InitializerInterface;

/**
 * Class AuthenticationServiceAwareInitializer
 */
class AuthenticationServiceAwareInitializer implements InitializerInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $instance)
    {
        if (!$instance instanceof AuthenticationServiceAwareInterface) {
            return;
        }

        $instance->setAuthenticationService($container->get(AuthenticationServiceInterface::class));
    }
}
