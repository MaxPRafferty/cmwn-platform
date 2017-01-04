<?php

namespace Job\Service;

use Application\Utils\NoopLoggerAwareTrait;
use Job\Feature\JobQueueFeatureInterface;
use Job\JobInterface;
use Zend\Log\LoggerInterface;

/**
 * A Job Service that talks to Resque
 */
class JobService implements JobServiceInterface
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
        $config = $config['job-service'] ?? [];
        $this->queuePrefix = $config['queue-prefix'] ?? '';
        $this->setLogger($logger);
    }

    /**
     * If the job will state it's own queue use that otherwise set the name to default
     *
     * Prepends the job prefix if the prefix is not empty
     *
     * @param $job
     * @return string
     */
    protected function getJobQueue($job)
    {
        $queueName = ($job instanceof JobQueueFeatureInterface ? $job->getQueue() : 'default');
        if (!empty($this->queuePrefix)) {
            $queueName = sprintf('%s-%s', $this->queuePrefix, $queueName);
        }

        return $queueName;
    }

    /**
     * @inheritdoc
     */
    public function sendJob(JobInterface $job)
    {
        try {
            return $returnJob =  \Resque::enqueue(
                $this->getJobQueue($job),
                get_class($job),
                $job->getArrayCopy()
            );
        } catch (\Exception $redisException) {
            $this->getLogger()->crit($redisException->getMessage());
        }
    }
}
