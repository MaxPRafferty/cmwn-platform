<?php

namespace Skribble\Service;

use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class SkribbleServiceFactory
 */
class SkribbleServiceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var TableGateway $tableGateway */
        $tableGateway = $serviceLocator->get('Skribbles/Table');
        return new SkribbleService($tableGateway);
    }
}
