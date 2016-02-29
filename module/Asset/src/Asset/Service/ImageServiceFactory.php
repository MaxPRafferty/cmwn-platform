<?php

namespace Asset\Service;

use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ImageServiceFactory
 * @package Asset\Service
 * @codeCoverageIgnore
 */
class ImageServiceFactory implements FactoryInterface
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
        $tableGateway = $serviceLocator->get('ImagesTable');
        return new ImageService($tableGateway);
    }
}
