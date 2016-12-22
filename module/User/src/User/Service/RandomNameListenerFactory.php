<?php

namespace User\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class RandomNameListenerFactory
 */
class RandomNameListenerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new RandomNameListener($container->get('NamesTable'));
    }
}
