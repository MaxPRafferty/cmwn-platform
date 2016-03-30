<?php

namespace User\Service;

use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class RandomNameListenerFactory
 * @codeCoverageIgnore
 */
class RandomNameListenerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $services)
    {
        /** @var TableGateway $tableGateway */
        $tableGateway = $services->get('NamesTable');
        return new RandomNameListener($tableGateway);
    }
}
