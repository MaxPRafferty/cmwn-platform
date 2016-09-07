<?php

namespace Job\Service;

use Application\Utils\NoopLoggerAwareTrait;
use Job\Feature\JobQueueFeatureInterface;
use Job\JobInterface;
use Zend\Log\LoggerAwareInterface;

/**
 * Class JobService
 * @codeCoverageIgnore
 */
class JobService implements JobServiceInterface, LoggerAwareInterface
{
    use NoopLoggerAwareTrait;

    /**
     * @param $job
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
