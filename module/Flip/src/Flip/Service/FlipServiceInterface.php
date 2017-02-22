<?php

namespace Flip\Service;

use Application\Exception\NotFoundException;
use Flip\FlipInterface;
use Zend\Paginator\Adapter\AdapterInterface;

/**
 * A Service that handles flips
 */
interface FlipServiceInterface
{
    /**
     * Used to fetch a paginated list of flips
     *
     * @param null $where
     * @param FlipInterface|null $prototype
     *
     * @return AdapterInterface
     */
    public function fetchAll($where = null, FlipInterface $prototype = null): AdapterInterface;

    /**
     * Fetches a flip using by the flip id
     *
     * @param $flipId
     * @param FlipInterface|null $prototype
     *
     * @throws NotFoundException
     * @return FlipInterface
     */
    public function fetchFlipById($flipId, FlipInterface $prototype = null): FlipInterface;

    /**
     * @param FlipInterface $flip
     * @return bool
     */
    public function createFlip(FlipInterface $flip): bool;

    /**
     * @param FlipInterface $flip
     * @return bool
     */
    public function updateFlip(FlipInterface $flip): bool;

    /**
     * @param FlipInterface $flip
     * @return bool
     */
    public function deleteFlip(FlipInterface $flip): bool;
}
