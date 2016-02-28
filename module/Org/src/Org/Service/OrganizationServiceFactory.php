<?php

namespace Org\Service;

use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class UserServiceFactory
 *
 * @package User\Service
 * @codeCoverageIgnore
 */
class OrganizationServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var TableGateway $tableGateway */
        $tableGateway = $serviceLocator->get('OrganizationsTable');
        return new OrganizationService($tableGateway);
    }

}
