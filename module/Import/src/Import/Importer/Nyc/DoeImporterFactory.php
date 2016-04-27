<?php

namespace Import\Importer\Nyc;

use Group\Service\GroupServiceInterface;

use Import\Importer\Nyc\Parser\DoeParser;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class DoeImporterFactory
 */
class DoeImporterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $services)
    {
        /** @var DoeParser $parser */
        /** @var GroupServiceInterface $groupService */
        $parser       = $services->get(DoeParser::class);
        $groupService = $services->get(GroupServiceInterface::class);

        return new DoeImporter($parser, $groupService);
    }
}
