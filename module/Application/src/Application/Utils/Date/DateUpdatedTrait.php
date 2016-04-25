<?php

namespace Application\Utils\Date;

/**
 * Trait DateUpdatedTrait
 *
 * Trait to help a class define a date updated property
 */
trait DateUpdatedTrait
{
    /**
     * @var \DateTime
     */
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
