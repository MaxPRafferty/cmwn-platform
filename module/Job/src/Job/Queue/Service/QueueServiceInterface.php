<?php

namespace Job\Queue\Service;

use Job\JobInterface;

/**
 * Interface QueueServiceIterface
 * @package Queue\Service
 */
interface QueueServiceInterface
{
    /**
     * Sends a job to the queue
     *
     * @param JobInterface $job
     * @return mixed
     */
    public function sendJob(JobInterface $job);
}
