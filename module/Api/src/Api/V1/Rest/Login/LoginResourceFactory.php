<?php

namespace Api\V1\Rest\Login;

use Security\Authentication\AuthAdapter;
use Zend\Authentication\AuthenticationService;
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
        /** @var AuthenticationService $authService */
        /** @var AuthAdapter $adapter */
        $authService = $serviceLocator->get(AuthenticationService::class);
        $adapter     = $serviceLocator->get(AuthAdapter::class);
        return new LoginResource($authService, $adapter);
    }
}
