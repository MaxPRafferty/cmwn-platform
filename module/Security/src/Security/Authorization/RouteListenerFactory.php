<?php

namespace Security\Authorization;

use Interop\Container\ContainerInterface;
use Security\Service\SecurityGroupServiceInterface;
use Security\Service\SecurityOrgServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class RouterListenerFactory
 */
class RouteListenerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        $config = isset($config['cmwn-security']) ? $config['cmwn-security'] : [];

        return new RouteListener(
            $config,
            $container->get(SecurityOrgServiceInterface::class),
            $container->get(SecurityGroupServiceInterface::class)
        );
    }
}
