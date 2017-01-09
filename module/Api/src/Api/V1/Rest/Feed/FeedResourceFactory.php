<?php

namespace Api\V1\Rest\Feed;

use Feed\Service\FeedServiceInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class FeedResourceFactory
 * @package Api\V1\Rest\Feed
 */
class FeedResourceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new FeedResource(
            $container->get(FeedServiceInterface::class)
        );
    }
}
