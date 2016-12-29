<?php

namespace Api\Factory;

use Interop\Container\ContainerInterface;
use Security\Listeners\UserRouteListener;
use Security\Authorization\Assertion\UserAssertion;
use User\Service\UserServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class UserRouteListenerFactory
 */
class UserRouteListenerFactory implements FactoryInterface
{

    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new UserRouteListener(
            $container->get(UserServiceInterface::class),
            $container->get(UserAssertion::class)
        );
    }
}
