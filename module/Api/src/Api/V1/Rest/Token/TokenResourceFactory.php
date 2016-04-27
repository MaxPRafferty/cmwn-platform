<?php

namespace Api\V1\Rest\Token;

use Security\Authentication\AuthenticationService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class TokenResourceFactory
 * @package Api\V1\Rest\Token
 */
class TokenResourceFactory implements FactoryInterface
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
        return new TokenResource($authService);
    }
}
