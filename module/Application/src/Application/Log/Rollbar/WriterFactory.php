<?php

namespace Application\Log\Rollbar;

use Zend\Log\Filter\Priority;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class WriterFactory
 */
class WriterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var \RollbarNotifier $notifier */
        $notifier = $serviceLocator->get('Application\Log\Rollbar\Notifier');
        $options = $serviceLocator->get(Options::class);
        $writer   =  new Writer($notifier, $serviceLocator, $options);
        $writer->addFilter(new Priority($options->getLogLevel()));
        return $writer;
    }
}
