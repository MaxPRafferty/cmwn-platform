<?php

namespace Rule\Engine\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class SpecificationManagerFactory
 */
class SpecificationManagerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config     = $container->get('Config');
        $specConfig = $config['specifications'] ?? [];

        return new SpecificationManager($container, $specConfig);
    }
}
