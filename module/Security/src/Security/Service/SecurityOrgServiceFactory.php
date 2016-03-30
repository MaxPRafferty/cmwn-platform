<?php

namespace Security\Service;

use Zend\Db\Adapter\Adapter;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class SecurityOrgServiceFactory
 * @package Security\Service
 */
class SecurityOrgServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $services)
    {
        /** @var Adapter $adapter */
        $adapter   = $services->get('Zend\Db\Adapter\Adapter');
        return new SecurityOrgService($adapter);
    }
}
