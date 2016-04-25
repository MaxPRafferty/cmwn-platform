<?php

namespace Flip\Service;

use Application\Exception\NotFoundException;
use Application\Utils\ServiceTrait;
use Flip\Flip;
use Flip\FlipInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator\ArraySerializable;
use Zend\Paginator\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class FlipService
 */
class FlipService implements FlipServiceInterface
{
    use ServiceTrait;

    /**
     * @var TableGateway
     */
    protected $flipTableGateway;

    /**
     * GameService constructor.
     *
     * @param TableGateway $gateway
     */
    public function __construct(TableGateway $gateway)
    {
        $this->flipTableGateway = $gateway;
    }

    /**
     * Fetches all the flips
     *
     * @param null|PredicateInterface|array $where
     * @param null|object $prototype
     * @return AdapterInterface
     */
    public function fetchAll($where = null, $prototype = null)
    {
        $where     = $this->createWhere($where);
        $prototype = null === $prototype ? new Flip() : $prototype;
        $resultSet = new HydratingResultSet(new ArraySerializable(), $prototype);
        $select    = new Select($this->flipTableGateway->getTable());
        $select->where($where);
        return new DbSelect(
            $select,
            $this->flipTableGateway->getAdapter(),
            $resultSet
        );
    }

    /**
     * Fetches a flip by the flip Id
     *
     * @param $flipId
     * @return FlipInterface
     * @throws NotFoundException
     */
    public function fetchFlipById($flipId)
    {
        $rowSet = $this->flipTableGateway->select(['flip_id' => $flipId]);
        $row    = $rowSet->current();
        if (!$row) {
            throw new NotFoundException("Flip not Found");
        }

        return new Flip((array) $row);
    }
}
