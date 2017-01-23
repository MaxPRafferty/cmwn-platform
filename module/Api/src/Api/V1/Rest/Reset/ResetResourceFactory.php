<?php

namespace Api\V1\Rest\Reset;

use Forgot\Service\ForgotServiceInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class ResetResourceFactory
 */
class ResetResourceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new ResetResource($container->get(ForgotServiceInterface::class));
    }
}
