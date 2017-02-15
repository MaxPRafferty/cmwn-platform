<?php

namespace Application\Utils\Date;

/**
 * Helps satisfy SoftDeleteInterface
 *
 * @see SoftDeleteInterface
 */
trait SoftDeleteTrait
{
    use DateDeletedTrait;

    /**
     * Test if dateDeleted property is not null
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->dateDeleted !== null;
    }
}
