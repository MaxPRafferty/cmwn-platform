<?php

namespace Flip\Delegator;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\DelegatorFactoryInterface;

/**
 * Class FlipUserDelegatorFactory
 */
class FlipUserDelegatorFactory implements DelegatorFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $name, callable $callback, array $options = null)
    {
        return new FlipUserDelegator($callback());
    }
}
