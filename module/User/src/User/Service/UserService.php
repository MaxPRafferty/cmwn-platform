<?php

namespace User\Service;

use Application\Exception\NotFoundException;
use Application\Utils\ServiceTrait;
use Ramsey\Uuid\Uuid;
use User\StaticUserFactory;
use User\UserHydrator;
use User\UserInterface;
use User\User;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Json\Json;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class UserService
 *
 * @package User\Service
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class UserService implements UserServiceInterface
{
    use ServiceTrait;

    /**
     * @var TableGateway
     */
    protected $userTableGateway;

    /**
     * UserService constructor.
     * @param TableGateway $gateway
     */
    public function __construct(TableGateway $gateway)
    {
        $this->userTableGateway = $gateway;
    }

    /**
     * @inheritdoc
     * @param null|PredicateInterface|array $where
     * @param bool $paginate
     * @param null|object $prototype
     * @return HydratingResultSet|DbSelect
     */
    public function fetchAll($where = null, $paginate = true, $prototype = null)
    {
        $where     = $this->createWhere($where);
        $resultSet = new HydratingResultSet(new UserHydrator($prototype), $prototype);

        if ($paginate) {
            $select    = new Select(['u' => $this->userTableGateway->getTable()]);
            $select->where($where);
            $select->order(['u.first_name', 'u.last_name']);
            return new DbSelect(
                $select,
                $this->userTableGateway->getAdapter(),
                $resultSet
            );
        }

        $results = $this->userTableGateway->select($where);
        $resultSet->initialize($results);
        return $resultSet;
    }

    /**
     * Create a new user
     *
     * A User Id will be auto generated
     *
     * @param UserInterface $user
     * @return bool
     */
    public function createUser(UserInterface $user)
    {
        $user->setUpdated(new \DateTime());
        $user->setCreated(new \DateTime());
        $user->setUserId(Uuid::uuid1());

        $data            = $user->getArrayCopy();
        $data['meta']    = Json::encode($data['meta']);
        $data['user_id'] = $user->getUserId();
        $data['created'] = $user->getCreated()->format("Y-m-d H:i:s");
        $data['updated'] = $user->getUpdated()->format("Y-m-d H:i:s");
        $data['normalized_username'] = User::normalizeUsername($data['username']);

        unset($data['password']);
        unset($data['deleted']);
        unset($data['super']);

        $this->userTableGateway->insert($data);
        return true;
    }

    /**
     * Saves an Existing user
     *
     * @param UserInterface $user
     * @return bool
     * @throws NotFoundException
     */
    public function updateUser(UserInterface $user)
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

        $this->fetchUser($user->getUserId());

        $this->userTableGateway->update(
            $data,
            ['user_id' => $user->getUserId()]
        );

        return true;
    }

    /**
     * Updates the username if the user wants to update his own username
     * @param UserInterface $user
     * @param $username
     * @return bool
     */
    public function updateUserName(UserInterface $user, $username)
    {
        $this->userTableGateway->update(
            ['username' => $username],
            ['user_id' => $user->getUserId()]
        );
        return true;
    }

    /**
     * @param $array
     * @return \User\Adult|\User\Child
     * @throws NotFoundException
     */
    protected function fetchHelper($array)
    {
        $rowSet = $this->userTableGateway->select($array);
        $row    = $rowSet->current();
        if (!$row) {
            throw new NotFoundException("User not Found");
        }

        return StaticUserFactory::createUser($row->getArrayCopy());
    }
    /**
     * Fetches one user from the DB using the id
     *
     * @param $userId
     * @return UserInterface
     * @throws NotFoundException
     */
    public function fetchUser($userId)
    {
        return $this->fetchHelper(['user_id' => $userId]);
    }

    /**
     * Fetches one user from the DB using the external id
     *
     * @param $externalId
     * @return UserInterface
     * @throws NotFoundException
     */
    public function fetchUserByExternalId($externalId)
    {
        return $this->fetchHelper(['external_id' => $externalId]);
    }

    /**
     * Fetches one user from the DB using the email
     *
     * @param $email
     * @return UserInterface
     * @throws NotFoundException
     */
    public function fetchUserByEmail($email)
    {
        return $this->fetchHelper(['email' => $email]);
    }

    /**
     * @inheritdoc
     * @param $username
     * @return UserInterface
     * @throws NotFoundException
     */
    public function fetchUserByUsername($username)
    {
        return $this->fetchHelper(['username' => $username]);
    }

    /**
     * Deletes a user from the database
     *
     * Soft deletes unless soft is false
     *
     * @param UserInterface $user
     * @param bool $soft
     * @return bool
     */
    public function deleteUser(UserInterface $user, $soft = true)
    {
        $this->fetchUser($user->getUserId());

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

    /**
     * @param string $username
     * @return string
     */
    public static function normalizeUsername($username)
    {
        return strtolower(preg_replace('/((?![a-zA-Z0-9]+).)/', '', $username));
    }
}
