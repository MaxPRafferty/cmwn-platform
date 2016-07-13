<?php

namespace Api\V1\Rest\Skribble;

use Job\Aws\Sqs\SqsJobService;
use Skribble\Service\SkribbleServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class SkribbleResourceFactory
 */
class SkribbleResourceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var SkribbleServiceInterface $skribbleService */
        /** @var SqsJobService $jobService */
        $skribbleService = $serviceLocator->get(SkribbleServiceInterface::class);
        $jobService      = $serviceLocator->get('SkribbleSns');
        return new SkribbleResource($skribbleService, $jobService);
    }
}
