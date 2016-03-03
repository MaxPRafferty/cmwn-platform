<?php

namespace Api\V1\Rest\Login;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class LoginResourceFactory
 * @package Api\V1\Rest\Login
 */
class LoginResourceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new LoginResource($serviceLocator->get('Security\Authentication\CmwnAuthenticationService'));
    }
}
