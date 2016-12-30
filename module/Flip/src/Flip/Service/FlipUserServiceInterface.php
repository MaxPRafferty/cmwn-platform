<?php

namespace Flip\Service;

use Flip\EarnedFlipInterface;
use Zend\Paginator\Adapter\AdapterInterface;

/**
 * An Interface for a Service that handles flips for a user
 */
interface FlipUserServiceInterface
{
    /**
     * Fetches a paginated list of flips the user had earned
     *
     * This should group flips earned by the earliest flip
     *
     * @param $user
     * @param null $where
     * @param null|EarnedFlipInterface $prototype
     *
     * @return AdapterInterface
     */
    public function fetchEarnedFlipsForUser(
        $user,
        $where = null,
        EarnedFlipInterface $prototype = null
    ): AdapterInterface;

    /**
     * Attaches a flip to a user
     *
     * @param $user
     * @param $flip
     *
     * @return bool
     */
    public function attachFlipToUser($user, $flip): bool;
}
