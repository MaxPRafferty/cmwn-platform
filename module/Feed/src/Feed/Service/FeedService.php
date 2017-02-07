<?php

namespace Feed\Service;

use Application\Exception\NotFoundException;
use Application\Utils\ServiceTrait;
use Feed\Feed;
use Feed\FeedInterface;
use Ramsey\Uuid\Uuid;
use User\UserInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator\ArraySerializable;
use Zend\Json\Json;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class FeedService
 * @package Feed\Service
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FeedService implements FeedServiceInterface
{
    use ServiceTrait;

    /**
     * @var TableGateway
     */
    protected $tableGateway;

    /**
     * FeedService constructor.
     * @param TableGateway $tableGateway
     */
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @inheritdoc
     */
    public function createFeed(FeedInterface $feed)
    {
        $feed->setFeedId(Uuid::uuid1()->toString());
        $feed->setCreated(new \DateTime);

        $data = $feed->getArrayCopy();
        $data['sender'] = $data['sender'] instanceof UserInterface? $data['sender']->getUserId() : $data['sender'];
        $data['created'] = $feed->getCreated()->format('Y-m-d H-i-s');
        $data['updated'] = $data['created'];
        $data['posted']  = $data['posted'] === null
            ? $data['created']
            : ($data['posted'] instanceof \DateTime ? $data['posted']->format('Y-m-d H-i-s') : $data['posted']);

        $data['meta'] = Json::encode($data['meta']);
        unset($data['deleted']);

        $this->tableGateway->insert($data);
        return true;
    }

    /**
     * @inheritdoc
     */
    public function fetchFeed(string $feedId, $where = null, FeedInterface $prototype = null)
    {
        $prototype = $prototype === null ? new Feed() : $prototype;
        $where = $this->createWhere($where);
        $where->addPredicate(new Operator('feed_id', Operator::OP_EQ, $feedId));
        $rowSet = $this->tableGateway->select($where);

        $row = $rowSet->current();
        if (!$row) {
            throw new NotFoundException('Feed not found');
        }

        $prototype->exchangeArray($row->getArrayCopy());

        return $prototype;
    }

    /**
     * @inheritdoc
     */
    public function fetchAll($where = null, FeedInterface $prototype = null)
    {
        $where = $this->createWhere($where);
        $where->isNull('ft.deleted');
        $prototype = $prototype === null ? new Feed() : $prototype;
        $resultSet = new HydratingResultSet(new ArraySerializable(), $prototype);
        $select = new Select(['ft' => $this->tableGateway->getTable()]);
        $select->where($where);
        $select->order('priority DESC');
        return new DbSelect(
            $select,
            $this->tableGateway->getAdapter(),
            $resultSet
        );
    }

    /**
     * @inheritdoc
     */
    public function updateFeed(FeedInterface $feed)
    {
        $this->fetchFeed($feed->getFeedId());
        $data = $feed->getArrayCopy();
        unset($data['feed_id']);
        $data['updated'] = new \DateTime();
        $data['updated'] = $data['updated']->format('Y-m-d H-i-s');
        $data['posted'] = $data['posted'] instanceof \DateTime
            ? $data['posted']->format('Y-m-d H-i-s')
            : $data['posted'];
        $data['meta'] = Json::encode($data['meta']);
        $this->tableGateway->update($data, ['feed_id' => $feed->getFeedId()]);
        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteFeed(FeedInterface $feed, $soft = true)
    {
        $this->fetchFeed($feed->getFeedId());

        if ($soft) {
            $this->tableGateway->update(
                ['deleted' => (new \DateTime())->format('Y-m-d H-i-s')],
                ['feed_id' => $feed->getFeedId()]
            );
            return true;
        }

        $this->tableGateway->delete(['feed_id' => $feed->getFeedId()]);
        return true;
    }
}
