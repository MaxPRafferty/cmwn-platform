<?php

namespace Application\Listeners;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class ListenersAggregateFactory
 */
class ListenersAggregateFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        $config = isset($config['shared-listeners']) ? $config['shared-listeners'] : [];
        return new ListenersAggregate($container, $config);
    }
}
