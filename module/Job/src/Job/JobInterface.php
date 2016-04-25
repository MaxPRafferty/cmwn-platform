<?php

namespace Job;

/**
 * Interface JobInterface
 */
interface JobInterface
{
    /**
     * Performs the work for the job
     */
    public function perform();

    /**
     * Gets the data that will be passed for the job
     *
     * @return array
     */
    public function getArrayCopy();

    /**
     * Returns the arguement values back to the object
     *
     * @param array $data
     * @return mixed
     */
    public function exchangeArray(array $data);
}
