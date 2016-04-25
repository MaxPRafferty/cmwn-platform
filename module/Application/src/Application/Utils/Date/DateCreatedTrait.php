<?php

namespace Application\Utils\Date;

/**
 * Trait DateCreatedTrait
 *
 * Trait to help define a property for date created
 */
trait DateCreatedTrait
{
    /**
     * @var \DateTime
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
     * @return $this
     */
    public function setCreated($created)
    {
        $created = DateTimeFactory::factory($created);
        $this->dateCreated = $created;
        return $this;
    }
}
