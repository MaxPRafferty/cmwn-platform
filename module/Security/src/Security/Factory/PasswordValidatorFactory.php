<?php

namespace Security\Factory;

use Interop\Container\ContainerInterface;
use Security\PasswordValidator;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Class PasswordValidatorFactory
 */
class PasswordValidatorFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $validator = new PasswordValidator();
        $validator->setAuthenticationService($container->get(AuthenticationServiceInterface::class));
    }
}
