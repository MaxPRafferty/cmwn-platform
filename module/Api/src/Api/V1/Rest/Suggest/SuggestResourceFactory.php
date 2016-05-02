<?php

namespace Api\V1\Rest\Suggest;

use Friend\Service\SuggestedFriendServiceInterface;
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
        /** @var SuggestedFriendServiceInterface $suggestService */
        $suggestService = $serviceLocator->get(SuggestedFriendServiceInterface::class);
        return new SuggestResource($suggestService);
    }
}
