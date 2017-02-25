<?php

namespace Job\Service;

use Application\Utils\NoopLoggerAwareTrait;
use Job\Feature\JobQueueFeatureInterface;
use Job\JobInterface;
use Zend\Log\LoggerAwareInterface;
use Zend\Log\LoggerInterface;

/**
 * Class JobService
 * @codeCoverageIgnore
 */
class JobService implements JobServiceInterface, LoggerAwareInterface
{
    use NoopLoggerAwareTrait;

    /**
     * @var string a prefix for the queue
     */
    protected $queuePrefix;

    /**
     * @inheritDoc
     */
    public function __construct(array $config, LoggerInterface $logger)
    {
        $config            = $config['job-service'] ?? [];
        $this->queuePrefix = $config['queue-prefix'] ?? '';
        $this->setLogger($logger);
    }

    /**
     * If the job will state it's own queue use that otherwise set the name to default
     *
     * Prepends the job prefix if the prefix is not empty
     *
     * @param $job
     *
     * @return string
     */
    protected function getJobQueue($job)
    {
        return $job instanceof JobQueueFeatureInterface ? $job->getQueue() : 'default';
    }

    /**
     * @param JobInterface $job
     * @return string
     */
    public function sendJob(JobInterface $job)
    {
        try {
            return $returnJob = \Resque::enqueue(
                $this->getJobQueue($job),
                get_class($job),
                $job->getArrayCopy()
            );
        } catch (\Exception $redisException) {
            $this->getLogger()->crit($redisException->getMessage());
        }
    }
}
