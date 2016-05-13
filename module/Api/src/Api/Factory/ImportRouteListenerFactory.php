<?php

namespace Api\Factory;

use Api\Listeners\ImportRouteListener;
use Security\Service\SecurityOrgService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ImportRouteListenerFactory
 */
class ImportRouteListenerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var SecurityOrgService $ordService */
        $orgService = $serviceLocator->get(SecurityOrgService::class);
        return new ImportRouteListener($orgService);
    }
}
