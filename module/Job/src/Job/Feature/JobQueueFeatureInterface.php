<?php

namespace Job\Feature;

/**
 * Interface JobTubeFeatureInterface
 */
interface JobQueueFeatureInterface
{
    /**
     * @return string
     */
    public function getQueue();
}
