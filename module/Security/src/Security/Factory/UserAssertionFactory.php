<?php

namespace Security\Factory;

use Interop\Container\ContainerInterface;
use Security\Authorization\Assertions\UserAssertion;
use Security\Service\SecurityGroupServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class UserAssertionFactory
 */
class UserAssertionFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new UserAssertion($container->get(SecurityGroupServiceInterface::class));
    }
}
