<?php

namespace Api\V1\Rest\Token;

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
        return new TokenResource($serviceLocator->get('Security\Authentication\CmwnAuthenticationService'));
    }
}
