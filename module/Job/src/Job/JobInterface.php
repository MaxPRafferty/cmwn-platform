<?php

namespace Job;

/**
 * Interface JobInterface
 *
 * @package Job
 */
interface JobInterface
{
    const JOB_PROCESSING = 1;
    const JOB_COMPLETED  = 2;
    const JOB_ERROR      = 4;
    const JOB_RESPAWN    = 8;

    /**
     * Runs the code for the job
     *
     * Returning false will prevent further events listeners from triggering
     *
     * @return bool
     */
    public function run();

    /**
     * Should be of one the states above.
     *
     * @return int
     */
    public function getJobState();

    /**
     * The Id of the job from the queue
     *
     * @return string
     */
    public function getJobId();

    /**
     * Sets the Id of the job
     *
     * @param $jobId
     * @return $this
     */
    public function setJobId($jobId);

    /**
     * Data that will be passed along in the message
     *
     * @return array
     */
    public function getJobData();

    /**
     * Sets the data that was passed from getJobData
     *
     * @param array $jobData
     * @return $this
     */
    public function setJobData(array $jobData);
}
