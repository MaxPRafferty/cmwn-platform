<?php

namespace Api\V1\Rest\GroupReset;

use Interop\Container\ContainerInterface;
use Security\Service\SecurityServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class GroupResetResourceFactory
 */
class GroupResetResourceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new GroupResetResource($container->get(SecurityServiceInterface::class));
    }
}
