<?php

namespace Api\V1\Rest\Import;

use Job\Service\JobServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ImportResourceFactory
 */
class ImportResourceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $services)
    {
        /** @var JobServiceInterface $jobService */
        $jobService = $services->get('Job\Service');
        return new ImportResource($jobService, $services);
    }
}