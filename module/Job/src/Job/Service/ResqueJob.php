<?php

namespace Job\Service;

use Job\JobInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class Job
 */
class ResqueJob extends \Resque_Job
{
    /**
     * @var object  Override the instance var since it is private
     */
    protected $jobInstance;

    /**
     * @var ServiceLocatorInterface
     */
    protected $services;

    /**
     * ResqueJob constructor.
     *
     * @param string $queue
     * @param array $payload
     * @param ServiceLocatorInterface $services
     */
    public function __construct($queue, array $payload, ServiceLocatorInterface $services)
    {
        $this->services = $services;
        parent::__construct($queue, $payload);
    }

    /**
     * @param string $queue
     * @param ServiceLocatorInterface $services
     * @return bool|ResqueJob
     */
    public static function reserve($queue, ServiceLocatorInterface $services)
    {
        $payload = \Resque::pop($queue);
        if (!is_array($payload)) {
            return false;
        }

        return new ResqueJob($queue, $payload, $services);
    }

    /**
     * This loads the job from the service manager and sets the arguements back
     *
     * @return object
     */
    public function getInstance()
    {
        if (null !== $this->jobInstance) {
            return $this->jobInstance;
        }

        $serviceName = isset($this->payload['class']) ? $this->payload['class'] : null;

        if (!$this->services->has($serviceName)) {
            throw new \Resque_Job_DirtyExitException(sprintf('No Service found for "%s"', $serviceName));
        }

        $job = $this->services->get($serviceName);

        if (!$job instanceof JobInterface) {
            throw new \Resque_Job_DirtyExitException(sprintf('Job "%s" does not implement JobInterface', $serviceName));
        }

        $job->exchangeArray($this->getArguments());
        return $this->jobInstance;
    }
}
