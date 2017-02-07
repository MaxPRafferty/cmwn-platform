<?php

namespace Flip\Service;

use Application\Exception\NotFoundException;
use Flip\EarnedFlipInterface;
use User\UserInterface;
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

    /**
     * Acknowledges a flip
     *
     * @param EarnedFlipInterface $earnedFlip
     *
     * @return bool
     */
    public function acknowledgeFlip(EarnedFlipInterface $earnedFlip): bool;

    /**
     * Fetches a paginated list of all the times a user earned a flip
     *
     * @param UserInterface $user
     * @param string $flipId
     * @param EarnedFlipInterface|null $prototype
     *
     * @return AdapterInterface
     */
    public function fetchFlipsForUser(
        UserInterface $user,
        string $flipId,
        EarnedFlipInterface $prototype = null
    ): AdapterInterface;

    /**
     * Fetches the latest flip that needs to be acknowledged
     *
     * @param UserInterface $user
     * @param EarnedFlipInterface $prototype
     *
     * @throws NotFoundException
     * @return EarnedFlipInterface
     */
    public function fetchLatestAcknowledgeFlip(
        UserInterface $user,
        EarnedFlipInterface $prototype = null
    ): EarnedFlipInterface;
}
