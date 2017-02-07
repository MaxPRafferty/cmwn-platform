<?php

namespace Forgot\Service;

use Interop\Container\ContainerInterface;
use Security\Service\SecurityServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class ForgotServiceFactory
 */
class ForgotServiceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new ForgotService($container->get(SecurityServiceInterface::class));
    }
}
