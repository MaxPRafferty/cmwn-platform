<?php

namespace Api\V1\Rest\Feed;

use Feed\Service\FeedServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class FeedResourceFactory
 * @package Api\V1\Rest\Feed
 */
class FeedResourceFactory implements FactoryInterface
{
    /**@inheritdoc*/
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $feedService = $serviceLocator->get(FeedServiceInterface::class);
        return new FeedResource($feedService);
    }
}
