<?php

namespace Skribble\Service;

use Application\Exception\NotFoundException;
use Application\Utils\ServiceTrait;
use Ramsey\Uuid\Uuid;
use Skribble\Skribble;
use Skribble\SkribbleInterface;
use User\UserInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator\ArraySerializable;
use Zend\Json\Json;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class SkribbleService
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SkribbleService implements SkribbleServiceInterface
{
    use ServiceTrait;

    /**
     * @var TableGateway
     */
    protected $gateway;

    /**
     * SecurityService constructor.
     *
     * @param TableGateway $gateway
     */
    public function __construct(TableGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * Fetches all the Skribbles for a user
     *
     * @param $user
     * @param null $where
     * @param null $prototype
     *
     * @return DbSelect
     */
    public function fetchAllForUser($user, $where = null, $prototype = null)
    {
        $userId = $user instanceof UserInterface ? $user->getUserId() : $user;
        $where  = $this->createWhere($where);
        $where->addPredicate(new Expression('(created_by = ? OR friend_to = ?)', $userId, $userId));

        return $this->buildAdapter($where, $prototype);
    }

    /**
     * Fetches all Received Skribbles for a user
     *
     * @param $user
     * @param null $where
     * @param null $prototype
     *
     * @return DbSelect
     */
    public function fetchReceivedForUser($user, $where = null, $prototype = null)
    {
        $userId = $user instanceof UserInterface ? $user->getUserId() : $user;
        $where  = $this->createWhere($where);
        $where->addPredicate(new Operator('friend_to', '=', $userId));

        return $this->buildAdapter($where, $prototype);
    }

    /**
     * Fetches all Sent Skribbles for a user
     *
     * @param $user
     * @param null $where
     * @param null $prototype
     *
     * @return DbSelect
     */
    public function fetchSentForUser($user, $where = null, $prototype = null)
    {
        $userId = $user instanceof UserInterface ? $user->getUserId() : $user;
        $where  = $this->createWhere($where);
        $where->addPredicate(new Operator('status', '=', SkribbleInterface::STATUS_COMPLETE));
        $where->addPredicate(new Operator('created_by', '=', $userId));

        return $this->buildAdapter($where, $prototype);
    }

    /**
     * Fetches all Draft Skribbles for a user
     *
     * @param $user
     * @param null $where
     * @param null $prototype
     *
     * @return DbSelect
     */
    public function fetchDraftForUser($user, $where = null, $prototype = null)
    {
        $userId = $user instanceof UserInterface ? $user->getUserId() : $user;
        $where = $this->createWhere($where);
        $where->addPredicate(new Operator('status', '=', SkribbleInterface::STATUS_NOT_COMPLETE));
        $where->addPredicate(new Operator('created_by', '=', $userId));

        return $this->buildAdapter($where, $prototype);
    }

    /**
     * Fetches a skribble from the db
     *
     * @param $skribbleId
     * @param null $prototype
     *
     * @return null|SkribbleInterface
     * @throws NotFoundException
     */
    public function fetchSkribble($skribbleId, $prototype = null)
    {
        $prototype = $prototype === null ? new Skribble() : $prototype;
        $rowSet    = $this->gateway->select(['skribble_id' => $skribbleId]);
        $row       = $rowSet->current();
        if (!$row) {
            throw new NotFoundException("Skribble not Found");
        }

        $hydrator = new ArraySerializable();
        $hydrator->hydrate($row->getArrayCopy(), $prototype);

        return $prototype;
    }

    /**
     * Creates a new Skribble
     *
     * @param SkribbleInterface $skribble
     *
     * @return bool
     */
    public function createSkribble(SkribbleInterface $skribble)
    {
        $skribble->setSkribbleId(Uuid::uuid1());
        $skribble->setCreated(new \DateTime());
        $skribble->setUpdated(new \DateTime());

        $data = $skribble->getArrayCopy();
        unset($data['deleted']);

        $data['rules']   = Json::encode($skribble->getRules()->getArrayCopy());
        $data['created'] = $skribble->getCreated()->format("Y-m-d H:i:s");
        $data['updated'] = $skribble->getUpdated()->format("Y-m-d H:i:s");

        $this->gateway->insert($data);

        return true;
    }

    /**
     * Updates a skribble
     *
     * @param SkribbleInterface $skribble
     *
     * @return bool
     */
    public function updateSkribble(SkribbleInterface $skribble)
    {
        $skribble->setUpdated(new \DateTime());

        $data            = $skribble->getArrayCopy();
        $data['rules']   = Json::encode($skribble->getRules()->getArrayCopy());
        $data['updated'] = $skribble->getUpdated()->format("Y-m-d H:i:s");
        unset($data['created']);

        $this->fetchSkribble($skribble->getSkribbleId());
        $this->gateway->update(
            $data,
            ['skribble_id' => $skribble->getSkribbleId()]
        );

        return true;
    }

    /**
     * Deletes the Skribble
     *
     * @param SkribbleInterface|string $skribble
     * @param bool $hard
     *
     * @return int
     */
    public function deleteSkribble($skribble, $hard = false)
    {
        $skribble = $skribble instanceof SkribbleInterface
            ? $skribble
            : $this->fetchSkribble($skribble);

        $where = ['skribble_id' => $skribble->getSkribbleId()];
        if ($hard) {
            return (bool)$this->gateway->delete($where);
        }

        $skribble->setDeleted(new \DateTime());
        $this->gateway->update(
            ['deleted' => $skribble->getDeleted()->format('Y-m-d H:i:s')],
            $where
        );

        return true;
    }

    /**
     * Helper to build the pagination adapter
     *
     * @param Where $where
     * @param null $prototype
     *
     * @return DbSelect
     */
    protected function buildAdapter(Where $where, $prototype = null)
    {
        $prototype = $prototype === null ? new Skribble() : $prototype;
        $resultSet = new HydratingResultSet(new ArraySerializable(), $prototype);

        $select = new Select(['s' => $this->gateway->getTable()]);
        $select->where($where);

        return new DbSelect(
            $select,
            $this->gateway->getAdapter(),
            $resultSet
        );
    }
}
