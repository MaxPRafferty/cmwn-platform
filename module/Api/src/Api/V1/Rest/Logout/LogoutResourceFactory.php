<?php

namespace Api\V1\Rest\Logout;

use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class LogoutResourceFactory
 * @package Api\V1\Rest\Logout
 */
class LogoutResourceFactory implements FactoryInterface
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
        $authService = $serviceLocator->get(AuthenticationService::class);
        return new LogoutResource($authService);
    }
}
