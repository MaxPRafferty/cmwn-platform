<?php

namespace Security\Factory;

use Interop\Container\ContainerInterface;
use Security\Guard\CsrfGuard;
use Security\Listeners\HttpAuthListener;
use Zend\Authentication\Adapter\Http;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class HttpAuthListenerFactory
 */
class HttpAuthListenerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new HttpAuthListener($container->get(Http::class), $container->get(CsrfGuard::class));
    }
}
