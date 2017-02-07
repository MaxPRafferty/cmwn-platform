<?php

namespace Flag;

use Interop\Container\ContainerInterface;
use User\Service\UserServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 *
 * Class FlagHydratorFactory
 */
class FlagHydratorFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new FlagHydrator($container->get(UserServiceInterface::class));
    }
}
