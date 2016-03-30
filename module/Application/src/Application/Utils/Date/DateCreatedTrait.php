<?php

namespace Application\Utils\Date;

/**
 * Trait DateCreatedTrait
 *
 * @author Chuck "MANCHUCK" Reeves <chuck@manchuck.com>
 */
trait DateCreatedTrait
{
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
