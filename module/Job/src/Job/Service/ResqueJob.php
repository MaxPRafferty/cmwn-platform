<?php

namespace Job\Service;

use Application\Utils\NoopLoggerAwareTrait;
use Job\JobInterface;
use Job\Processor\JobRunner;
use Zend\Log\LoggerAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class Job
 * @codeCoverageIgnore
 */
class ResqueJob extends \Resque_Job implements LoggerAwareInterface
{
    use NoopLoggerAwareTrait;

    /**
     * @var JobRunner  Override the instance var since it is private
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
     * Find the next available job from the specified queue and return an
     * instance of Resque_Job for it.
     *
     * @param string $queue The name of the queue to check for a job in.
     * @return null|object Null when there aren't any waiting jobs, instance of Resque_Job when a job was found.
     */
    public static function reserve($queue)
    {
        throw new \RuntimeException('Do not call native reserve for this job');
    }


    /**
     * @param string $queue
     * @param ServiceLocatorInterface $services
     * @return bool|ResqueJob
     */
    public static function reserveJob($queue, ServiceLocatorInterface $services)
    {
        $payload = \Resque::pop($queue);
        if (!is_array($payload)) {
            return false;
        }

        return new ResqueJob($queue, $payload, $services);
    }

    /**
     * Creates a JobRunner
     *
     * The job is loaded from the SM and passed the data so it can be sanitized by the job
     *
     * @return JobRunner
     */
    public function getInstance()
    {
        if (null !== $this->jobInstance) {
            return $this->jobInstance;
        }

        $serviceName = isset($this->payload['class']) ? $this->payload['class'] : null;

        if (!$this->services->has($serviceName)) {
            $this->getLogger()->crit(sprintf('Service with name %s was not found', $serviceName));
            throw new \Resque_Job_DirtyExitException(sprintf('No Service found for "%s"', $serviceName));
        }

        // We create the job so we can pass the params though to the job
        // this way it can sanitize the data before execution
        $job = $this->services->get($serviceName);

        if (!$job instanceof JobInterface) {
            $this->getLogger()->crit(sprintf('Job "%s" does not implement JobInterface', $serviceName));
            throw new \Resque_Job_DirtyExitException(sprintf('Job "%s" does not implement JobInterface', $serviceName));
        }

        $job->setLogger($this->getLogger());

        try {
            $this->getLogger()->notice('Prepping job');
            /** @var JobRunner $runner */
            $runner = $this->services->get(JobRunner::class);

            $runner->setLogger($this->getLogger());
            $job->exchangeArray($this->getArguments());
            $runner->setJob($serviceName, $job->getArrayCopy());
        } catch (\RuntimeException $jobException) {
            $msg = sprintf('Error creating job %s: %s', $serviceName, $jobException->getMessage());
            $this->getLogger()->emerg($msg);
            throw new \Resque_Job_DirtyExitException($msg);
        }

        $this->jobInstance = $runner;
        return $this->jobInstance;
    }
}
