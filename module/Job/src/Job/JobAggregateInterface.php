<?php

namespace Job;

use Job\Queue\Service\QueueServiceInterface;

/**
 * Interface JobAggregateInterface
 * @package Job
 */
interface JobAggregateInterface
{
    /**
     * Allows a job to spawn another job
     *
     * This will only be called if the aggregating jobs state is JOB_COMPLETED
     *
     * @param QueueServiceInterface $queueService
     * @return mixed
     */
    public function aggregateJob(QueueServiceInterface $queueService);
}
