<?php

namespace Application\Utils\Date;

/**
 * Trait to help satisfy DateCreatedInterface
 *
 * @see DateCreatedInterface
 *
 */
trait DateCreatedTrait
{
    /**
     * @var \DateTime|null
     */
    protected $dateCreated;

    /**
     * @return \DateTime|null
     */
    public function getCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param \DateTime|string|null $created
     *
     * @return $this
     */
    public function setCreated($created)
    {
        $created           = DateTimeFactory::factory($created);
        $this->dateCreated = $created;

        return $this;
    }

    /**
     * Gets the data formatted or null if not set
     *
     * @param string $format
     *
     * @return null|string
     */
    protected function formatCreated(string $format)
    {
        return ($this->dateCreated !== null) ? $this->dateCreated->format($format) : null;
    }
}
