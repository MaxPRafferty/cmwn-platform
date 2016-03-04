<?php

namespace Job\Feature;

/**
 * Interface TubeInterface
 * @package Job\Feature
 */
interface TubeInterface
{
    /**
     * Allows a job to be run in a different tube
     *
     * @return string
     */
    public function getTube();
}
