<?php

namespace Job\Feature;

/**
 * Class DelayInterface
 * @package Job\Feature
 */
interface DelayInterface
{
    /**
     * Allows a job to be delayed
     *
     * Time MUST BE in seconds
     *
     * @return int
     */
    public function getJobDelay();
}
