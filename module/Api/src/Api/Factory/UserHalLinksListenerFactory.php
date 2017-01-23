<?php

namespace Api\Factory;

use Api\Listeners\UserHalLinksListener;
use Interop\Container\ContainerInterface;
use Security\Service\SecurityGroupServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class UserHalLinksListenerFactory
 */
class UserHalLinksListenerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new UserHalLinksListener($container->get(SecurityGroupServiceInterface::class));
    }
}
