<?php
namespace Api\V1\Rest\Password;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class PasswordResourceFactory
 *
 * @codeCoverageIgnore
 */
class PasswordResourceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var \Zend\Authentication\AuthenticationService $authService */
        $authService = $serviceLocator->get('Security\Authentication\AuthenticationService');
        /** @var \Security\Service\SecurityService $securityService */
        $securityService = $serviceLocator->get('\Security\Service\SecurityService');
        return new PasswordResource($authService, $securityService);
    }
}
