<?php

namespace Flip\Delegator;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\DelegatorFactoryInterface;

/**
 * Class FlipDelegatorFactory
 */
class FlipDelegatorFactory implements DelegatorFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $name, callable $callback, array $options = null)
    {
        return new FlipDelegator($callback());
    }
}
