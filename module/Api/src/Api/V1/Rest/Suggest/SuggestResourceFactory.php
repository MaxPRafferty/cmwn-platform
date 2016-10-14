<?php

namespace Api\V1\Rest\Suggest;

use Suggest\Service\SuggestedServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class SuggestResourceFactory
 */
class SuggestResourceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var SuggestedServiceInterface $suggestService */
        $suggestService = $serviceLocator->get(SuggestedServiceInterface::class);
        return new SuggestResource($suggestService);
    }
}
