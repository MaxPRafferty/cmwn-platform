<?php

namespace Security\Authentication;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class CmwnAuthenticationAdapterFactory
 * @package Security\Authentication
 * @codeCoverageIgnore
 */
class ApiAdapterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var \Zend\Authentication\AuthenticationService $authService */
        $authService = $serviceLocator->get('Security\Authentication\AuthenticationService');

        /** @var \Security\Service\SecurityOrgService $securityService */
        $securityService = $serviceLocator->get('Security\Service\SecurityOrgService');
        return new ApiAdapter($authService, $securityService);
    }
}
