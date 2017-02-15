<?php

namespace Application\Log;

use Interop\Container\ContainerInterface;
use Zend\Log\PsrLoggerAdapter;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class PsrLoggerFactory
 */
class PsrLoggerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new PsrLoggerAdapter($container->get('Log\App'));
    }
}
