<?php

namespace Application\Utils\Date;

/**
 * Trait DateDeletedTrait
 *
 * @author Chuck "MANCHUCK" Reeves <chuck@manchuck.com>
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
     * @return $this
     */
    public function setDeleted($deleted)
    {
        $deleted = DateTimeFactory::factory($deleted);
        $this->dateDeleted = $deleted;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDeleted()
    {
        return $this->dateDeleted !== null;
    }
}
