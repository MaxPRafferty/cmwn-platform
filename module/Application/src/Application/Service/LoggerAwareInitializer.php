<?php

namespace Application\Service;

use Zend\Log\Logger;
use Zend\Log\LoggerAwareInterface;
use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Class LoggerAwareInitializer
 *
 * ${CARET}
 */
class LoggerAwareInitializer implements InitializerInterface
{
    /**
     * Initialize
     *
     * @param $instance
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        if (!$instance instanceof LoggerAwareInterface) {
            return;
        }

        $serviceLocator = $serviceLocator instanceof ServiceLocatorAwareInterface
            ? $serviceLocator->getServiceLocator()
            : $serviceLocator;

        if (!$serviceLocator->has('Log\App')) {
            return ;
        }

        /** @var Logger $logger */
        $logger = $serviceLocator->get('Log\App');
        $instance->setLogger($logger);
    }
}
