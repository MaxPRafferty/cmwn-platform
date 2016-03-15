<?php

namespace Job\Feature;

use Zend\Log\LoggerAwareInterface;

/**
 * Trait DryRunInterface
 */
trait DryRunTrait
{
    /**
     * @var bool
     */
    protected $dryRun = false;

    /**
     * Sets the dry run flag
     *
     * @param $dryRun
     */
    public function setDryRun($dryRun)
    {
        if ($this instanceof LoggerAwareInterface) {
            $this->getLogger()->info('Setting dry run flag');
        }

        $this->dryRun = (bool) $dryRun;
    }

    /**
     * @return bool
     */
    public function isDryRun()
    {
        return $this->dryRun;
    }

}
