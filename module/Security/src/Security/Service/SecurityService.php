<?php

namespace Security\Service;

use Application\Exception\NotFoundException;
use Lcobucci\JWT\Configuration;
use Security\SecurityUser;
use User\User;
use User\Service\UserService;
use User\UserInterface;
use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\Db\TableGateway\TableGateway;

/**
 * Class SecurityService
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
    public function fetchUserByEmail($email)
    {
        $rowSet = $this->gateway->select(['email' => $email]);
        $row    = $rowSet->current();
        if (!$row) {
            throw new NotFoundException("User not Found");
        }

        return new SecurityUser($row->getArrayCopy());
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

        $rowSet = $this->gateway->select($predicateSet);
        $row    = $rowSet->current();
        if ($row) {
            return new SecurityUser($row->getArrayCopy());
        }

        $rowSet = $this->gateway->select(['normalized_username' => UserService::normalizeUsername($username)]);
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
        $jwtConfig = new Configuration();
        $token     = $jwtConfig->createBuilder()
            ->canOnlyBeUsedBy($userId)
            ->issuedAt(time())
            ->canOnlyBeUsedAfter($start->getTimestamp())
            ->expiresAt($expires->getTimestamp())
            ->identifiedBy($code)
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
    public static function encryptPassword($password)
    {
        // cost 10
        return password_hash($password, PASSWORD_DEFAULT, ['cost' => 10]);
    }
}
