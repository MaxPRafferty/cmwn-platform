<?php

namespace Security\Authorization;

use Security\Service\SecurityOrgService;
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
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var \Security\Service\SecurityOrgService $orgService */
        $orgService  = $serviceLocator->get(SecurityOrgService::class);

        $config = $serviceLocator->get('config');
        $config = isset($config['cmwn-security']) ? $config['cmwn-security'] : [];
        return new RouteListener($config, $orgService);
    }
}
