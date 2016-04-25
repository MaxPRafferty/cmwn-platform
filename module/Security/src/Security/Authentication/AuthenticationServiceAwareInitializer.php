<?php

namespace Security\Authentication;

use Zend\Authentication\AuthenticationServiceInterface;
use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class AuthenticationServiceAwareInitializer
 *
 * ${CARET}
 */
class AuthenticationServiceAwareInitializer implements InitializerInterface
{
    /**
     * Initialize
     *
     * @param $instance
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        if (!$instance instanceof AuthenticationServiceAwareInterface) {
            return;
        }

        /** @var AuthenticationServiceInterface $authService */
        $authService = $serviceLocator->get(AuthenticationServiceInterface::class);
        $instance->setAuthenticationService($authService);
    }
}
