<?php

namespace Application\Log\Rollbar;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class NotifierFactory
 */
class NotifierFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new \RollbarNotifier($container->get(Options::class)->toArray());
    }
}
