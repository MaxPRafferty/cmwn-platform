<?php

namespace Api\Factory;

use Api\Listeners\ImportRouteListener;
use Security\Service\SecurityOrgServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ImportRouteListenerFactory
 */
class ImportRouteListenerFactory implements FactoryInterface
{
    /**
     * @inheritdoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var SecurityOrgServiceInterface $orgService */
        $orgService = $serviceLocator->get(SecurityOrgServiceInterface::class);
        return new ImportRouteListener($orgService);
    }
}
