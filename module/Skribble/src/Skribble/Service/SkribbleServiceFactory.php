<?php

namespace Skribble\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class SkribbleServiceFactory
 */
class SkribbleServiceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new SkribbleService($container->get('SkribblesTable'));
    }
}
