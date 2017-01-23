<?php
namespace Api\V1\Rest\Password;

use Interop\Container\ContainerInterface;
use Security\Authentication\AuthenticationService;
use Security\Service\SecurityServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class PasswordResourceFactory
 */
class PasswordResourceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new PasswordResource(
            $container->get(AuthenticationService::class),
            $container->get(SecurityServiceInterface::class)
        );
    }
}
