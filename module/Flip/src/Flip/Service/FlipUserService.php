<?php

namespace Flip\Service;

use Application\Utils\Date\DateTimeFactory;
use Application\Utils\ServiceTrait;
use Flip\EarnedFlip;
use Flip\EarnedFlipInterface;
use Flip\FlipInterface;
use User\UserInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Hydrator\ArraySerializable;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class FlipUserService
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
     * @param $user
     * @param null $where
     * @param null $prototype
     * @return DbSelect
     */
    public function fetchEarnedFlipsForUser($user, $where = null, $prototype = null)
    {
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

        $prototype = !$prototype instanceof EarnedFlipInterface ? new EarnedFlip() : $prototype;
        $resultSet = new HydratingResultSet(new ArraySerializable(), $prototype);
        return new DbSelect(
            $select,
            $this->pivotTable->getAdapter(),
            $resultSet
        );
    }

    /**
     * @param $user
     * @param $flip
     * @return bool
     */
    public function attachFlipToUser($user, $flip)
    {
        $userId = $user instanceof UserInterface ? $user->getUserId() : $user;
        $flipId = $flip instanceof FlipInterface ? $flip->getFlipId() : $flip;
        $earned = DateTimeFactory::factory('now');

        $this->pivotTable->insert([
            'user_id' => $userId,
            'flip_id' => $flipId,
            'earned'  => $earned->format(\DateTime::ISO8601)
        ]);

        return true;
    }
}
