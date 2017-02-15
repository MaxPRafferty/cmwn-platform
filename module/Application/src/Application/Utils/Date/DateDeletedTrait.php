<?php

namespace Application\Utils\Date;

/**
 * Trait to help satisfy DateDeletedInterface
 *
 * @see DateDeletedInterface
 */
trait DateDeletedTrait
{
    /**
     * @var \DateTime|null
     */
    protected $dateDeleted;

    /**
     * @return \DateTime|null
     */
    public function getDeleted()
    {
        return $this->dateDeleted;
    }

    /**
     * @param \DateTime|string|null $deleted
     *
     * @return $this
     */
    public function setDeleted($deleted)
    {
        $deleted           = DateTimeFactory::factory($deleted);
        $this->dateDeleted = $deleted;

        return $this;
    }

    /**
     * Gets the data formatted or null if not set
     *
     * @param string $format
     *
     * @return null|string
     */
    protected function formatDeleted(string $format)
    {
        return ($this->dateDeleted !== null) ? $this->dateDeleted->format($format) : null;
    }
}
