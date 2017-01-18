<?php

namespace Security\Service;

use Application\Exception\NotFoundException;
use Lcobucci\JWT\Builder;
use Security\SecurityUser;
use User\User;
use User\UserInterface;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

/**
 * Class SecurityService
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SecurityService implements SecurityServiceInterface
{
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
        $this->gateway    = $gateway;
    }

    /**
     * @inheritdoc
     */
    public function fetchUserByUserId($userId)
    {
        return $this->fetchHelper(['user_id' => $userId]);
    }

    /**
     * @inheritdoc
     */
    public function fetchUserByEmail($email)
    {
        return $this->fetchHelper(['email' => $email]);
    }

    /**
     * @inheritdoc
     */
    public function fetchUserByUserName($username)
    {
        $predicateSet = new PredicateSet([
            new Operator('username', Operator::OP_EQ, $username),
            new Operator('normalized_username', Operator::OP_EQ, User::normalizeUsername($username)),
        ], PredicateSet::OP_OR);

        return $this->fetchHelper($predicateSet);
    }

    /**
     * @param array | PredicateSet $where
     * @return SecurityUser
     * @throws NotFoundException
     */
    protected function fetchHelper($where)
    {
        $rowSet = $this->gateway->select($where);
        $row    = $rowSet->current();

        if (!$row) {
            throw new NotFoundException("User not Found");
        }

        return new SecurityUser($row->getArrayCopy());
    }

    /**
     * @inheritdoc
     */
    public function savePasswordToUser($user, $password)
    {
        $userId = $user instanceof UserInterface ? $user->getUserId() : $user;

        $this->gateway->update(
            ['password' => static::encryptPassword($password), 'code' => null],
            ['user_id' => $userId]
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function setSuper($user, $super = true)
    {
        $bit    = $super ? 1 : 0;
        $userId = $user instanceof UserInterface ? $user->getUserId() : $user;

        $this->gateway->update(
            ['super' => $bit],
            ['user_id' => $userId]
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function saveCodeToUser($code, $user, $days = 1, \DateTime $start = null)
    {
        $userId = $user instanceof UserInterface ? $user->getUserId() : $user;
        $start  = null === $start ? new \DateTime('now') : $start;
        $start->setTime(00, 00, 00);

        $expires = clone $start;
        $expires->add(new \DateInterval(sprintf('P%dD', abs($days))));
        $expires->setTime(23, 59, 59);
        $token     = (new Builder())
            ->setAudience($userId)
            ->setIssuedAt(time())
            ->setNotBefore($start->getTimestamp())
            ->setExpiration($expires->getTimestamp())
            ->setId($code)
            ->getToken();

        $this->gateway->update(
            ['code' => $token->__toString()],
            ['user_id' => $userId]
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function saveCodeToGroup($code, $groupId)
    {
        $select = new Select(['u' => $this->gateway->getTable()]);
        $select->columns(['user_id']);
        $select->join(
            ['ug' => 'user_groups'],
            'u.user_id = ug.user_id',
            [],
            Select::JOIN_LEFT
        );
        $select->join(
            ['cg' => 'groups'],
            'ug.group_id = cg.group_id',
            [],
            Select::JOIN_LEFT
        );
        $select->join(
            ['g' => 'groups'],
            '(cg.head BETWEEN g.head AND g.tail) AND (cg.network_id = g.network_id)',
            [],
            Select::JOIN_LEFT
        );

        $select->where(['g.group_id' => $groupId, 'u.type' => User::TYPE_CHILD, 'u.code is not null']);

        $users = $this->gateway->selectWith($select);

        foreach ($users as $user) {
            $user = $user->getArrayCopy();
            if (isset($user['user_id'])) {
                $userId = $user['user_id'];
                $this->saveCodeToUser($code, $userId);
            }
        }

        return true;
    }

    /**
     * Encrypts the password
     *
     * @param $password
     * @return mixed
     */
    public static function encryptPassword($password)
    {
        // cost 10
        return password_hash($password, PASSWORD_DEFAULT, ['cost' => 10]);
    }
}
