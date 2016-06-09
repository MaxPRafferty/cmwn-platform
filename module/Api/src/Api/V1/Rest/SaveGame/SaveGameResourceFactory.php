<?php

namespace Api\V1\Rest\SaveGame;

use Game\Service\SaveGameServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class SaveGameResourceFactory
 */
class SaveGameResourceFactory implements FactoryInterface
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
        /** @var SaveGameServiceInterface $saveService */
        $saveService = $serviceLocator->get(SaveGameServiceInterface::class);
        return new SaveGameResource($saveService);
    }
}
