<?php

namespace Application\Utils;

/**
 * Interface SoftDeleteInterface
 *
 * @author Chuck "MANCHUCK" Reeves <chuck@manchuck.com>
 */
interface SoftDeleteInterface
{
    /**
     * @return bool
     */
    public function isDeleted();
}
