<?php

namespace Application\Log;

use Application\Log\Rollbar\Writer;
use Interop\Container\ContainerInterface;
use Zend\Log\Filter\Priority;
use Zend\Log\Logger;
use Zend\Log\LoggerAbstractServiceFactory;
use Zend\Log\Writer\Noop;

/**
 * Creates a ZF Logger
 */
class LoggerFactory extends LoggerAbstractServiceFactory
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $logger = parent::__invoke($container, $requestedName, $options);

        /** @var Writer $writer */
        // TODO get the writer from a config
        $writer   = (defined('TEST_MODE') && TEST_MODE) ? new Noop() : $container->get(Writer::class);
        $priority = (defined('TEST_MODE') && TEST_MODE) ? Logger::DEBUG : Logger::ERR;
        $writer->addFilter(new Priority($priority));
        $logger->addWriter($writer);

        return $logger;
    }
}
