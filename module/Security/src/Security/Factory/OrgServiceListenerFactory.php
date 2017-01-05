<?php

namespace Security\Factory;

use Group\Service\UserGroupServiceInterface;
use Interop\Container\ContainerInterface;
use Security\Listeners\OrgServiceListener;
use Zend\ServiceManager\Factory\FactoryInterface;
use Security\Service\SecurityOrgServiceInterface;

/**
 * Class OrgServiceListenerFactory
 */
class OrgServiceListenerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new OrgServiceListener(
            $container->get(SecurityOrgServiceInterface::class),
            $container->get(UserGroupServiceInterface::class)
        );
    }
}
