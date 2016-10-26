<?php

namespace Api\V1\Rest\Flag;

use Flag\Service\FlagServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class FlagResourceFactory
 * @package Api\V1\Rest\Flag
 */
class FlagResourceFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return FlagResource
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $flagService = $serviceLocator->get(FlagServiceInterface::class);

        return new FlagResource($flagService);
    }
}
