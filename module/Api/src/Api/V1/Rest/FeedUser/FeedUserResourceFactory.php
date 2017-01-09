<?php

namespace Api\V1\Rest\FeedUser;

use Feed\Service\FeedUserServiceInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class FeedUserResourceFactory
 * @package Api\V1\Rest\FeedUser
 */
class FeedUserResourceFactory implements FactoryInterface
{
    /**@inheritdoc*/
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $feedUserService = $container->get(FeedUserServiceInterface::class);
        return new FeedUserResource($feedUserService);
    }
}
