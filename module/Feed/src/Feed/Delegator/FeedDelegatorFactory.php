<?php

namespace Feed\Delegator;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\DelegatorFactoryInterface;

/**
 * Class FeedDelegatorFactory
 * @package Feed\Delegator
 */
class FeedDelegatorFactory implements DelegatorFactoryInterface
{
    /**@inheritdoc*/
    public function __invoke(ContainerInterface $container, $name, callable $callback, array $options = null)
    {
        return new FeedDelegator($callback());
    }
}
