<?php

namespace Job\Feature;

/**
 *
 * Interface TimeToRunInterface
 * @package Job\Feature
 */
interface TimeToRunInterface
{
    /**
     * Allows a job to set how long it should take to run
     *
     * By default a job is set to run in 60 seconds
     *
     * Time MUST BE in seconds
     * 
     * @return int
     */
    public function getTimeToRun();
}
