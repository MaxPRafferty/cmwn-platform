<?php

namespace Security\Factory;

use Group\Service\UserGroupService;
use Interop\Container\ContainerInterface;
use Security\Listeners\OrgServiceListener;
use Security\Service\SecurityOrgService;
use Zend\ServiceManager\Factory\FactoryInterface;

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
            $container->get(SecurityOrgService::class),
            $container->get(UserGroupService::class)
        );
    }
}
