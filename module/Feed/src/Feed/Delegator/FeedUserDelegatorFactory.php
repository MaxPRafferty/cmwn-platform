<?php

namespace Feed\Delegator;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\DelegatorFactoryInterface;

/**
 * Class FeedUserDelegatorFactory
 * @package Feed\Delegator
 */
class FeedUserDelegatorFactory implements DelegatorFactoryInterface
{
    /**
     * @inheritdoc
     */
    public function __invoke(ContainerInterface $container, $name, callable $callback, array $options = null)
    {
        return new FeedUserDelegator($callback());
    }
}
