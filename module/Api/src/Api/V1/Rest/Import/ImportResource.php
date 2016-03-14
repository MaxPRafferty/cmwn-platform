<?php
namespace Api\V1\Rest\Import;

use Import\ImporterInterface;
use Job\Service\JobServiceInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZF\ApiProblem\ApiProblem;
use ZF\Rest\AbstractResourceListener;

/**
 * Class ImportResource
 */
class ImportResource extends AbstractResourceListener
{
    /**
     * @var JobServiceInterface
     */
    protected $jobService;

    /**
     * @var ServiceLocatorInterface
     */
    protected $services;

    public function __construct(JobServiceInterface $jobService, ServiceLocatorInterface $services)
    {
        $this->jobService = $jobService;
        $this->services   = $services;
    }

    /**
     * Create a resource
     *
     * @param  mixed $data
     * @return ApiProblem|mixed
     */
    public function create($data)
    {
        $type = $this->getInputFilter()->getValue('type');
        $job  = $this->services->get($type);

        if (!$job instanceof ImporterInterface) {
            return new ApiProblem(500, 'Invalid importer type');
        }

        $file = $this->getInputFilter()->getValue('file');
        $job->setFileName($file);

        $token = $this->jobService->sendJob($job);
        return new ImportEntity($token);
    }
}
