<?php

namespace Flip\Service;

use Zend\Paginator\Adapter\DbSelect;

/**
 * Interface FlipUserServiceInterface
 */
interface FlipUserServiceInterface
{
    /**
     * Fetches all the earned flips for a user
     *
     * @param $user
     * @param null $where
     * @param null $prototype
     * @return DbSelect
     */
    public function fetchEarnedFlipsForUser($user, $where = null, $prototype = null);

    /**
     * Attaches a flip to a user
     *
     * @param $user
     * @param $flip
     * @return bool
     */
    public function attachFlipToUser($user, $flip);
}
