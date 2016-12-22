<?php

namespace Suggest\Service;

use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class SuggestedFriendServiceFactory
 */
class SuggestedServiceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new SuggestedService(
            new TableGateway('user_suggestions', $container->get(Adapter::class))
        );
    }
}
