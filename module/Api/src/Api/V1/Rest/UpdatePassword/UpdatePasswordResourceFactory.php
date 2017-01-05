<?php

namespace Api\V1\Rest\UpdatePassword;

use Interop\Container\ContainerInterface;
use Security\Service\SecurityService;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class UpdatePasswordResourceFactory
 */
class UpdatePasswordResourceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new UpdatePasswordResource($container->get(SecurityService::class));
    }
}
