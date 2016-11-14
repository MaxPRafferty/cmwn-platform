<?php

namespace Api\V1\Rest\RestoreDb;

use RestoreDb\Service\RestoreDbService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class RestoreDbResourceFactory
 * @package Api\V1\Rest\RestoreDb
 */
class RestoreDbResourceFactory implements FactoryInterface
{
    /**
     * @inheritdoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $restoreDbService = $serviceLocator->get(RestoreDbService::class);
        return new RestoreDbResource($restoreDbService);
    }
}
