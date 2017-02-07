<?php

namespace Application\Service;

use Interop\Container\ContainerInterface;
use Zend\Log\LoggerAwareInterface;
use Zend\ServiceManager\Initializer\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Class LoggerAwareInitializer
 */
class LoggerAwareInitializer implements InitializerInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $instance)
    {
        if (!$instance instanceof LoggerAwareInterface) {
            return;
        }

        if (!$container->has('Log\App')) {
            return ;
        }

        $instance->setLogger($container->get('Log\App'));
    }
}
