<?php

namespace Import;

use Job\JobInterface;

/**
 * Interface To Import a file
 *
 * @package Import
 */
interface ImporterInterface extends JobInterface
{
    /**
     * Sets the name of the file to import
     *
     * @param $file
     * @return $this
     */
    public function setFileName($file);
}
