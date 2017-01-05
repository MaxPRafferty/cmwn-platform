<?php

namespace Security\Factory;

use Interop\Container\ContainerInterface;
use Security\Guard\CsrfGuard;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class CsrfGuardFactory
 */
class CsrfGuardFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        $config = isset($config['cmwn-security']) ? $config['cmwn-security'] : [];

        return new CsrfGuard($config);
    }
}
