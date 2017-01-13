<?php

namespace Feed\Service;

use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class FeedUserServiceFactory
 * @package Feed\Service
 */
class FeedUserServiceFactory implements FactoryInterface
{
    /**@inheritdoc*/
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new FeedUserService(
            new TableGateway('user_feed', $container->get(Adapter::class))
        );
    }
}
