<?php

namespace Application\Log;

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

        /** @var \Application\Log\Rollbar\Writer $writer */
        $writer = $container->get('Application\Log\Rollbar\Writer');
        $logger->addWriter($writer);
        return $logger;
    }
}
