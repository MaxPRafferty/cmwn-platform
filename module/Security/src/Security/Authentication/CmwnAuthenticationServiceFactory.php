<?php

namespace Security\Authentication;

use Zend\Authentication\Storage\Session;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class CmwnAuthenticationServiceFactory
 * @package Security\Authentication
 */
class CmwnAuthenticationServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var CmwnAuthenticationAdapter $adapter */
        $adapter = $serviceLocator->get('Security\Authentication\CmwnAuthenticationAdapter');
        return new CmwnAuthenticationService(new Session(), $adapter);
    }

}
