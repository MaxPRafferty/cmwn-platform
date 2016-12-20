<?php

namespace Flip\Service;

use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class FlipUserServiceFactory
 */
class FlipUserServiceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        // TODO Why not use the abstract factory for this?
        return new FlipUserService(
            new TableGateway('user_flips', $container->get(Adapter::class))
        );
    }
}
