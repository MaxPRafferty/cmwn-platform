<?php

namespace Api\V1\Rest\FlipUser;

use Flip\Service\FlipUserServiceInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class FlipUserResourceFactory
 */
class FlipUserResourceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new FlipUserResource($container->get(FlipUserServiceInterface::class));
    }
}
