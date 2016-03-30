<?php

namespace Job\Feature;

/**
 * Interface DryRunInterface
 *
 * ${CARET}
 */
interface DryRunInterface
{
    /**
     * @param bool $dryRun
     */
    public function setDryRun($dryRun);

    /**
     * @return bool
     */
    public function isDryRun();
}
