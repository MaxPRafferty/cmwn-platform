<?php


namespace Security\Authentication;


use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class CmwnAuthenticationAdapterFactory
 * @package Security\Authentication
 * @codeCoverageIgnore
 */
class CmwnAuthenticationAdapterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var \Security\Service\SecurityService $securityService */
        $securityService = $serviceLocator->get('Security\Service\SecurityService');
        return new CmwnAuthenticationAdapter($securityService);
    }

}
