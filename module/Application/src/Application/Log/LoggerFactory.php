<?php

namespace Application\Log;

use Application\Log\Rollbar\Writer;
use Interop\Container\ContainerInterface;
use Zend\Log\LoggerAbstractServiceFactory;
use Zend\Log\Writer\Noop;
use Zend\Log\Writer\Stream;

/**
 * Class LoggerFactory
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
        $writer = (defined('TEST_MODE') && TEST_MODE) ? new Noop() : $container->get(Writer::class);
        $logger->addWriter($writer);
        $file = realpath(__DIR__ . '/../../../../../data/logs/') . '/app.log';
        $logger->addWriter(new Stream($file));
        return $logger;
    }
}
