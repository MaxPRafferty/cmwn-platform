<?php

namespace Feed\Service;

use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class FeedServiceFactory
 * @package Feed\Service
 */
class FeedServiceFactory implements FactoryInterface
{
    /**
     * @inheritdoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new FeedService(
            new TableGateway('feed', $container->get(Adapter::class))
        );
    }
}
