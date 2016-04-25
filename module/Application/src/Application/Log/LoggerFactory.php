<?php

namespace Application\Log;

use Application\Log\Rollbar\Writer;
use Interop\Container\ContainerInterface;
use Zend\Log\LoggerAbstractServiceFactory;

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
        $writer = $container->get(Writer::class);
        $logger->addWriter($writer);
        return $logger;
    }
}
