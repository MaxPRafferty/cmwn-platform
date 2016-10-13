<?php


namespace Notice\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class NoticeControllerFactory
 * @package Notice\Controller
 */
class NoticeControllerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return NoticeController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceLocator = $serviceLocator instanceof ServiceLocatorAwareInterface
            ? $serviceLocator->getServiceLocator()
            : $serviceLocator;
        return new NoticeController($serviceLocator);
    }
}
