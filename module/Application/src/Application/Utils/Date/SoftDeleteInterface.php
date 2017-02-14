<?php

namespace Application\Utils\Date;

/**
 * An interface that allows an object to be soft deleted
 */
interface SoftDeleteInterface extends DateDeletedInterface
{
    /**
     * @return bool
     */
    public function isDeleted();
}
