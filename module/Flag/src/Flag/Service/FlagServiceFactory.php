<?php

namespace Flag\Service;

use Flag\FlagHydrator;
use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class FlagServiceFactory
 */
class FlagServiceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new FlagService(
            new TableGateway('image_flags', $container->get(Adapter::class)),
            $container->get(FlagHydrator::class)
        );
    }
}
