<?php

namespace Game\Service;

use Application\Exception\NotFoundException;
use Application\Utils\ServiceTrait;
use Game\Game;
use Group\GroupInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator\ArraySerializable;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class GameService
 * @package Game\Service
 */
class GameService implements GameServiceInterface
{
    use ServiceTrait;

    /**
     * @var TableGateway
     */
    protected $gameTableGateway;

    /**
     * GameService constructor.
     *
     * @param TableGateway $gateway
     */
    public function __construct(TableGateway $gateway)
    {
        $this->gameTableGateway = $gateway;
    }

    /**
     * @param null|PredicateInterface|array $where
     * @param bool $paginate
     * @param null|object $prototype
     * @return HydratingResultSet|DbSelect
     */
    public function fetchAll($where = null, $paginate = true, $prototype = null)
    {
        $where     = $this->createWhere($where);
        $resultSet = new HydratingResultSet(new ArraySerializable(), $prototype);

        if ($paginate) {
            $select    = new Select(['g' => $this->gameTableGateway->getTable()]);
            $select->where($where);
            $select->order(['g.title']);

            return new DbSelect(
                $select,
                $this->gameTableGateway->getAdapter(),
                $resultSet
            );
        }

        $results = $this->gameTableGateway->select($where);
        $resultSet->initialize($results);
        return $resultSet;
    }

    /**
     * Fetches one game from the DB using the id
     *
     * @param $groupId
     * @return GroupInterface
     * @throws NotFoundException
     */
    public function fetchGame($groupId)
    {
        $rowSet = $this->gameTableGateway->select(['game_id' => $groupId]);
        $row    = $rowSet->current();
        if (!$row) {
            throw new NotFoundException("Game not Found");
        }

        return new Game((array) $row);
    }
}
