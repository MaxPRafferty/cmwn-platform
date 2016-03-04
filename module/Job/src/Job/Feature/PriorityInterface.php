<?php

namespace Job\Feature;

/**
 * Interface PriorityInterface
 * @package Job\Feature
 */
interface PriorityInterface
{
    /**
     * Sets the priority for this job
     *
     * @return int
     */
    public function getPriority();
}
