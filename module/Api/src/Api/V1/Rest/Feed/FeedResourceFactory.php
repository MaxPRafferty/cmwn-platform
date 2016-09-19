<?php

namespace Api\V1\Rest\Feed;

use Game\Service\GameService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FeedResourceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $gameService = $serviceLocator->get(GameService::class);
        return new FeedResource($gameService);
    }
}
