<?php

namespace Api\V1\Rest\Feed;

use Game\Service\GameService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class FeedResourceFactory
 */
class FeedResourceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new FeedResource(
            $container->get(GameService::class)
        );
    }
}
