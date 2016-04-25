<?php

namespace Security\Authentication;

use Security\Service\SecurityOrgService;
use Security\Service\SecurityServiceInterface;
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
        /** @var SecurityServiceInterface $securityService */
        /** @var SecurityOrgService $orgService */
        $securityService = $serviceLocator->get(SecurityServiceInterface::class);
        $orgService      = $serviceLocator->get(SecurityOrgService::class);
        return new AuthAdapter($securityService, $orgService);
    }
}
