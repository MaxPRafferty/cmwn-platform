<?php

namespace Api\V1\Rest\FeedUser;

use Feed\Service\FeedUserServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class FeedUserResourceFactory
 * @package Api\V1\Rest\FeedUser
 */
class FeedUserResourceFactory implements FactoryInterface
{
    /**@inheritdoc*/
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $feedUserService = $serviceLocator->get(FeedUserServiceInterface::class);
        return new FeedUserResource($feedUserService);
    }
}
