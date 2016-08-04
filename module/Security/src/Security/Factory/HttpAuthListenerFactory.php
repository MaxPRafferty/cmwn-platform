<?php

namespace Security\Factory;

use Security\Guard\CsrfGuard;
use Security\Listeners\HttpAuthListener;
use Zend\Authentication\Adapter\Http;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class HttpAuthListenerFactory
 */
class HttpAuthListenerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $adapter = $serviceLocator->get(Http::class);
        $guard   = $serviceLocator->get(CsrfGuard::class);
        return new HttpAuthListener($adapter, $guard);
    }
}
