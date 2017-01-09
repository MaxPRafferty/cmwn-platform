<?php

namespace Application\Log\Rollbar;

use Interop\Container\ContainerInterface;
use Zend\Log\Filter\Priority;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class WriterFactory
 */
class WriterFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $options = $container->get(Options::class);
        $writer  = new Writer(
            $container->get('Application\Log\Rollbar\Notifier'),
            $container,
            $options
        );

        $writer->addFilter(new Priority($options->getLogLevel()));

        return $writer;
    }
}
