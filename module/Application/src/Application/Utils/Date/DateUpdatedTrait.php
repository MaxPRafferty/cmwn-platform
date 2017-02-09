<?php

namespace Application\Utils\Date;

/**
 * Trait to help satisfy DateUpdatedInterface
 *
 * @see DateUpdatedInterface
 *
 */
trait DateUpdatedTrait
{
    /**
     * @var \DateTime|null
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
     *
     * @return $this
     */
    public function setUpdated($updated)
    {
        $updated           = DateTimeFactory::factory($updated);
        $this->dateUpdated = $updated;

        return $this;
    }

    /**
     * Gets the data formatted or null if not set
     *
     * @param string $format
     *
     * @return null|string
     */
    protected function formatUpdated(string $format)
    {
        return ($this->dateUpdated !== null) ? $this->dateUpdated->format($format) : null;
    }
}
