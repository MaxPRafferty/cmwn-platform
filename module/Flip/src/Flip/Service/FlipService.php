<?php

namespace Flip\Service;

use Application\Exception\NotFoundException;
use Application\Utils\ServiceTrait;
use Flip\Flip;
use Flip\FlipInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator\ArraySerializable;
use Zend\Paginator\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Service used to fetch flips from the database
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
     * @inheritdoc
     */
    public function fetchAll($where = null, FlipInterface $prototype = null): AdapterInterface
    {
        $where     = $this->createWhere($where);
        $prototype = $prototype ?? new Flip();
        $resultSet = new HydratingResultSet(new ArraySerializable(), $prototype);
        $select    = new Select(['f' => $this->flipTableGateway->getTable()]);
        $select->where($where);
        $select->order(['f.title']);

        return new DbSelect(
            $select,
            $this->flipTableGateway->getAdapter(),
            $resultSet
        );
    }

    /**
     * @inheritdoc
     */
    public function fetchFlipById($flipId, FlipInterface $prototype = null): FlipInterface
    {
        $rowSet = $this->flipTableGateway->select(['flip_id' => $flipId]);
        $row    = $rowSet->current();
        if (!$row) {
            throw new NotFoundException("Flip not Found");
        }

        $flip = $prototype ?? new Flip();
        $flip->exchangeArray((array)$row);

        return $flip;
    }
}
