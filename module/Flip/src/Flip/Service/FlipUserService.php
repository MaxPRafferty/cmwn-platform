<?php

namespace Flip\Service;

use Application\Utils\Date\DateTimeFactory;
use Application\Utils\ServiceTrait;
use Flip\EarnedFlip;
use Flip\EarnedFlipInterface;
use Flip\FlipInterface;
use Ramsey\Uuid\Uuid;
use User\UserInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator\ArraySerializable;
use Zend\Paginator\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Service that handles flips a user has earned
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FlipUserService implements FlipUserServiceInterface
{
    use ServiceTrait;

    /**
     * @var TableGateway
     */
    protected $pivotTable;

    /**
     * GameService constructor.
     *
     * @param TableGateway $gateway
     */
    public function __construct(TableGateway $gateway)
    {
        $this->pivotTable = $gateway;
    }

    /**
     * @inheritdoc
     */
    public function fetchEarnedFlipsForUser(
        $user,
        $where = null,
        EarnedFlipInterface $prototype = null
    ): AdapterInterface {
        $where  = $this->createWhere($where);
        $userId = $user instanceof UserInterface ? $user->getUserId() : $user;
        $select = new Select(['f' => 'flips']);

        $where->addPredicate(new Expression('f.flip_id = uf.flip_id'));
        $select->join(
            ['uf' => 'user_flips'],
            new Expression('uf.user_id = ?', $userId),
            ['earned' => 'earned'],
            Select::JOIN_LEFT
        );

        $select->where($where);
        $select->group('f.flip_id');
        $select->order(['uf.earned', 'f.title']);

        $prototype = $prototype ?? new EarnedFlip();
        $resultSet = new HydratingResultSet(new ArraySerializable(), $prototype);

        return new DbSelect(
            $select,
            $this->pivotTable->getAdapter(),
            $resultSet
        );
    }

    /**
     * @inheritdoc
     */
    public function attachFlipToUser($user, $flip): bool
    {
        $userId = $user instanceof UserInterface ? $user->getUserId() : $user;
        $flipId = $flip instanceof FlipInterface ? $flip->getFlipId() : $flip;
        $earned = DateTimeFactory::factory('now');
        $ackId  = Uuid::uuid1();

        $this->pivotTable->insert([
            'user_id'        => $userId,
            'flip_id'        => $flipId,
            'earned'         => $earned->format(\DateTime::ISO8601),
            'acknowledge_id' => $ackId,
        ]);

        return true;
    }
}
