<?php

namespace Import\Importer\Nyc;

use Group\Service\GroupServiceInterface;
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
        /** @var \Import\Importer\Nyc\Parser\DoeParser $parser */
        $parser = $services->get('Import\Importer\Nyc\Parser\DoeParser');
        /** @var GroupServiceInterface $groupService */
        $groupService = $services->get('Group\Service\GroupService');
        return new DoeImporter($parser, $groupService);
    }
}
