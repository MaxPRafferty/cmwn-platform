<?php

namespace Application\Utils\Date;


/**
 * Trait DateUpdatedTrait
 *
 * @author Chuck "MANCHUCK" Reeves <chuck@manchuck.com>
 */
trait DateUpdatedTrait
{
    protected $dateUpdated;

    /**
     * @return \DateTime|null
     */
    public function getUpdated()
    {
        return $this->dateUpdated;
    }

    /**
     * @param \DateTime|null $updated
     * @return $this
     */
    public function setUpdated($updated)
    {
        $updated = DateTimeFactory::factory($updated);
        $this->dateUpdated = $updated;
        return $this;
    }
}
