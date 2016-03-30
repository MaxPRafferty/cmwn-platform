<?php

namespace Security\Authentication;

use Security\Service\SecurityOrgService;
use Security\Service\SecurityService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class AuthAdapterFactory
 * @package Security\Authentication
 */
class AuthAdapterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var SecurityService $securityService */
        $securityService = $serviceLocator->get('Security\Service\SecurityService');

        /** @var SecurityOrgService $orgService */
        $orgService   = $serviceLocator->get('Security\Service\SecurityOrgService');
        return new AuthAdapter($securityService, $orgService);
    }
}
