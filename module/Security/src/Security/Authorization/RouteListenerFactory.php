<?php

namespace Security\Authorization;

use Security\Service\SecurityGroupService;
use Security\Service\SecurityOrgServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class RouterListenerFactory
 */
class RouteListenerFactory implements FactoryInterface
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
        /** @var \Security\Service\SecurityOrgServiceInterface $orgService */
        $orgService = $serviceLocator->get(SecurityOrgServiceInterface::class);

        /** @var SecurityGroupService $groupService */
        $groupService = $serviceLocator->get(SecurityGroupService::class);

        $config = $serviceLocator->get('config');
        $config = isset($config['cmwn-security']) ? $config['cmwn-security'] : [];

        return new RouteListener($config, $orgService, $groupService);
    }
}
