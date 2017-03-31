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
use Zend\Json\Json;
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
    protected $gateway;

    /**
     * @var ArraySerializable
     */
    protected $hydrator;

    /**
     * GameService constructor.
     *
     * @param TableGateway $gateway
     */
    public function __construct(TableGateway $gateway)
    {
        $this->gateway  = $gateway;
        $this->hydrator = new ArraySerializable();
    }

    /**
     * @param FlipInterface $flip
     *
     * @return array
     */
    protected function getDataForDb(FlipInterface $flip)
    {
        return [
            'flip_id'     => $flip->getFlipId(),
            'title'       => $flip->getTitle(),
            'description' => $flip->getDescription(),
            'uris'        => Json::encode($flip->getUris()),
        ];
    }

    /**
     * @inheritdoc
     */
    public function fetchAll($where = null, FlipInterface $prototype = null): AdapterInterface
    {
        $where     = $this->createWhere($where);
        $prototype = $prototype ?? new Flip();
        $resultSet = new HydratingResultSet($this->hydrator, $prototype);
        $select    = new Select(['f' => $this->gateway->getTable()]);
        $select->where($where);
        $select->order(['f.title']);

        return new DbSelect(
            $select,
            $this->gateway->getAdapter(),
            $resultSet
        );
    }

    /**
     * @inheritdoc
     */
    public function fetchFlipById($flipId, FlipInterface $prototype = null): FlipInterface
    {
        $rowSet = $this->gateway->select(['flip_id' => $flipId]);
        $row    = $rowSet->current();
        if (!$row) {
            throw new NotFoundException("Flip not Found");
        }

        $flip = $prototype ?? new Flip();
        $this->hydrator->hydrate((array)$row, $flip);

        return $flip;
    }

    /**
     * @inheritdoc
     */
    public function createFlip(FlipInterface $flip): bool
    {
        return (bool) $this->gateway->insert($this->getDataForDb($flip));
    }

    /**
     * @inheritdoc
     */
    public function updateFlip(FlipInterface $flip): bool
    {
        $flipData = $this->getDataForDb($flip);
        unset($flipData['flip_id']);

        return (bool) $this->gateway->update(
            $flipData,
            ['flip_id' => $flip->getFlipId()]
        );
    }

    /**
     * @inheritdoc
     */
    public function deleteFlip(FlipInterface $flip): bool
    {
        return (bool) $this->gateway->delete(['flip_id' => $flip->getFlipId()]);
    }
}
