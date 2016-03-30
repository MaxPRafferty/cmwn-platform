<?php

namespace Job\Service;

use Job\JobInterface;

/**
 * Interface JobServiceInterface
 */
interface JobServiceInterface
{
    /**
     * @param JobInterface $job
     * @return string
     */
    public function sendJob(JobInterface $job);
}
