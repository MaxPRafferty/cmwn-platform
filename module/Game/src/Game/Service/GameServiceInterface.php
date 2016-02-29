<?php

namespace Game\Service;

use Application\Exception\NotFoundException;
use Group\GroupInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Interface GameServiceInterface
 *
 * @author Chuck "MANCHUCK" Reeves <chuck@manchuck.com>
 */
interface GameServiceInterface
{

    /**
     * @param null|PredicateInterface|array $where
     * @param bool $paginate
     * @param null|object $prototype
     * @return HydratingResultSet|DbSelect
     */
    public function fetchAll($where = null, $paginate = true, $prototype = null);

    /**
     * Fetches one game from the DB using the id
     *
     * @param $groupId
     * @return GroupInterface
     * @throws NotFoundException
     */
    public function fetchGame($groupId);
}
