<?php

namespace Application\Log\Rollbar;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class NotifierFactory
 */
class NotifierFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return \RollbarNotifier
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var Options $options */
        $options = $serviceLocator->get(Options::class);
        return new \RollbarNotifier($options->toArray());
    }
}
