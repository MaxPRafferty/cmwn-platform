<?php

namespace Security\Factory;

use Security\PasswordValidator;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class PasswordValidatorFactory
 */
class PasswordValidatorFactory implements FactoryInterface
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
        $validator      = new PasswordValidator();
        $serviceLocator = $serviceLocator instanceof ServiceLocatorAwareInterface
            ? $serviceLocator->getServiceLocator()
            : $serviceLocator;

        /** @var AuthenticationServiceInterface $authService */
        $authService = $serviceLocator->get(AuthenticationServiceInterface::class);
        $validator->setAuthenticationService($authService);
        
        return $validator;
    }
}
