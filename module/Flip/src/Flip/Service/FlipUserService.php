<?php

namespace Flip\Service;

use Application\Utils\ServiceTrait;
use User\UserInterface;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

/**
 * Class FlipUserService
 */
class FlipUserService
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
     * 
     * @param $user
     * @param null $where
     * @param null $prototype
     */
    public function fetchEarnedFlipsForUser($user, $where = null, $prototype = null)
    {
        $where  = $this->createWhere($where);
        $userId = $user instanceof UserInterface ? $user->getUserId() : $user;

        $select = new Select(['u' => 'users']);
        $select->join(
            ['uf' => 'user_flips'],
            'uf.user_id = u.user_id',
            ['user_flip_id' => 'flip_id'],
            Select::JOIN_LEFT
        );

        $select->join(
            ['f' => 'user_flips'],
            'f.flip_id = uf.flip_id',
            ['user_flip_id' => 'flip_id'],
            Select::JOIN_LEFT
        );


    }

    public function attachFlipToUser($user, $flip)
    {

    }
}
