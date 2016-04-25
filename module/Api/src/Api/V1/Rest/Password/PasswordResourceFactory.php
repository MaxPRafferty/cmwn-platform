<?php
namespace Api\V1\Rest\Password;

use Security\Authentication\AuthenticationService;
use Security\Service\SecurityServiceInterface;
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
        /** @var AuthenticationService $authService */
        /** @var SecurityServiceInterface $securityService */
        $authService     = $serviceLocator->get(AuthenticationService::class);
        $securityService = $serviceLocator->get(SecurityServiceInterface::class);
        return new PasswordResource($authService, $securityService);
    }
}
