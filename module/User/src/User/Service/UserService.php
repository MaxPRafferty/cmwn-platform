<?php

namespace User\Service;

use Application\Exception\NotFoundException;
use Application\Utils\ServiceTrait;
use Ramsey\Uuid\Uuid;
use User\UserHydrator;
use User\UserInterface;
use User\User;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Json\Json;
use Zend\Paginator\Adapter\AdapterInterface;
use Zend\Paginator\Adapter\DbSelect;

/**
 * A User service that talks to the database
 */
class UserService implements UserServiceInterface
{
    use ServiceTrait;

    /**
     * @var TableGateway
     */
    protected $userTableGateway;

    /**
     * @var UserHydrator
     */
    protected $hydrator;

    /**
     * UserService constructor.
     *
     * @param TableGateway $gateway
     */
    public function __construct(TableGateway $gateway)
    {
        $this->userTableGateway = $gateway;
        $this->hydrator         = new UserHydrator();
    }

    /**
     * @inheritdoc
     */
    public function fetchAll($where = null, UserInterface $prototype = null): AdapterInterface
    {
        $where     = $this->createWhere($where);
        $resultSet = new HydratingResultSet($this->hydrator, $prototype);

        $select = new Select(['u' => $this->userTableGateway->getTable()]);
        $select->where($where);
        $select->order(['u.first_name', 'u.last_name']);

        return new DbSelect(
            $select,
            $this->userTableGateway->getAdapter(),
            $resultSet
        );
    }

    /**
     * @inheritdoc
     */
    public function createUser(UserInterface $user): bool
    {
        $user->setUpdated(new \DateTime());
        $user->setCreated(new \DateTime());
        $user->setUserId(Uuid::uuid1());

        $data                        = $this->hydrator->extract($user);
        $data['meta']                = Json::encode($data['meta']);
        $data['user_id']             = $user->getUserId();
        $data['created']             = $user->getCreated()->format("Y-m-d H:i:s");
        $data['updated']             = $user->getUpdated()->format("Y-m-d H:i:s");
        $data['normalized_username'] = User::normalizeUsername($data['username']);

        unset($data['password']);
        unset($data['deleted']);
        unset($data['super']);
        unset($data['token']); // TODO Remove this check when the JWT is created
        unset($data['links']); // TODO Remove when ZF-Hal is respecting entities that are link collection aware

        $this->userTableGateway->insert($data);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function updateUser(UserInterface $user): bool
    {
        $user->setUpdated(new \DateTime());
        $data            = $user->getArrayCopy();
        $data['meta']    = Json::encode($data['meta']);
        $data['updated'] = $user->getUpdated()->format("Y-m-d H:i:s");
        if (isset($data['username'])) {
            $data['normalized_username'] = User::normalizeUsername($data['username']);
        }
        unset($data['password']);
        unset($data['deleted']);
        unset($data['super']);
        unset($data['type']);
        unset($data['username']);
        unset($data['token']); // TODO Remove this check when the JWT is created
        unset($data['links']); // TODO Remove when ZF-Hal is respecting entities that are link collection aware

        $this->fetchUser($user->getUserId(), null);

        $this->userTableGateway->update(
            $data,
            ['user_id' => $user->getUserId()]
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function updateUserName(UserInterface $user, string $username): bool
    {
        $this->userTableGateway->update(
            ['username' => $username],
            ['user_id' => $user->getUserId()]
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function fetchHelper($array, UserInterface $prototype = null)
    {
        $rowSet = $this->userTableGateway->select($array);
        $row    = $rowSet->current();
        if (!$row) {
            throw new NotFoundException("User not Found");
        }

        return $this->hydrator->hydrate($row->getArrayCopy(), $prototype);
    }

    /**
     * @inheritdoc
     */
    public function fetchUser(string $userId, UserInterface $prototype = null): UserInterface
    {
        return $this->fetchHelper(['user_id' => $userId], $prototype);
    }

    /**
     * @inheritdoc
     */
    public function fetchUserByExternalId(string $externalId, UserInterface $prototype = null): UserInterface
    {
        return $this->fetchHelper(['external_id' => $externalId], $prototype);
    }

    /**
     * @inheritdoc
     */
    public function fetchUserByEmail(string $email, UserInterface $prototype = null): UserInterface
    {
        return $this->fetchHelper(['email' => $email], $prototype);
    }

    /**
     * @inheritdoc
     */
    public function fetchUserByUsername(string $username, UserInterface $prototype = null): UserInterface
    {
        return $this->fetchHelper(['username' => $username], $prototype);
    }

    /**
     * @inheritdoc
     */
    public function deleteUser(UserInterface $user, bool $soft = true): bool
    {
        $this->fetchUser($user->getUserId(), null);

        if ($soft) {
            $user->setDeleted(new \DateTime());

            $this->userTableGateway->update(
                ['deleted' => $user->getDeleted()->format(\DateTime::ISO8601)],
                ['user_id' => $user->getUserId()]
            );

            return true;
        }

        $this->userTableGateway->delete(['user_id' => $user->getUserId()]);

        return true;
    }
}
