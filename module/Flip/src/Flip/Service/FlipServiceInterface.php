<?php

namespace Flip\Service;

use Application\Exception\NotFoundException;
use Flip\FlipInterface;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Paginator\Adapter\AdapterInterface;

/**
 * Interface FlipServiceInterface
 */
interface FlipServiceInterface
{

    /**
     * Fetches all the flips
     *
     * @param null|PredicateInterface|array $where
     * @param null|object $prototype
     * @return AdapterInterface
     */
    public function fetchAll($where = null, $prototype = null);

    /**
     * Fetches a flip by the flip Id
     *
     * @param $flipId
     * @return FlipInterface
     * @throws NotFoundException
     */
    public function fetchFlipById($flipId);
}
