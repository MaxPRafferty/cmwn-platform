<?php

namespace Import\Importer\Nyc;

use Group\Service\GroupServiceInterface;
use Import\Importer\Nyc\Parser\DoeParser;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class DoeImporterFactory
 */
class DoeImporterFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new DoeImporter(
            $container->get(DoeParser::class),
            $container->get(GroupServiceInterface::class)
        );
    }
}
