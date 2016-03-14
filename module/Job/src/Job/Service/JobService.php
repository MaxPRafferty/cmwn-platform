<?php

namespace Job\Service;

use Job\Feature\JobQueueFeatureInterface;
use Job\JobInterface;

/**
 * Class JobService
 */
class JobService implements JobServiceInterface
{
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
        return \Resque::enqueue(
            $this->getJobQueue($job),
            get_class($job),
            $job->getArrayCopy()
        );
    }
}
