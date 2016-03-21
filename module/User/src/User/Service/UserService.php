<?php

namespace User\Service;

use Application\Exception\NotFoundException;
use Ramsey\Uuid\Uuid;
use User\StaticUserFactory;
use User\UserHydrator;
use User\UserInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\TableGateway;
use Zend\Json\Json;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class UserService
 *
 * @package User\Service
 */
class UserService implements UserServiceInterface
{
    /**
     * @var TableGateway
     */
    protected $userTableGateway;

    public function __construct(TableGateway $gateway)
    {
        $this->userTableGateway = $gateway;
    }

    /**
     * @param null|PredicateInterface|array $where
     * @param bool $paginate
     * @param null|object $prototype
     * @return HydratingResultSet|DbSelect
     */
    public function fetchAll($where = null, $paginate = true, $prototype = null)
    {
        $where     = !$where instanceof PredicateInterface ? new Where($where) : $where;
        $resultSet = new HydratingResultSet(new UserHydrator($prototype), $prototype);

        if ($paginate) {
            $select    = new Select($this->userTableGateway->getTable());
            $select->where($where);
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

        unset($data['password']);
        unset($data['deleted']);
        unset($data['super']);

        $this->fetchUser($user->getUserId());

        $this->userTableGateway->update(
            $data,
            ['user_id' => $user->getUserId()]
        );

        return true;
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
        $rowset = $this->userTableGateway->select(['user_id' => $userId]);
        $row    = $rowset->current();
        if (!$row) {
            throw new NotFoundException("User not Found");
        }

        return StaticUserFactory::createUser($row->getArrayCopy());
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
        $rowset = $this->userTableGateway->select(['external_id' => $externalId]);
        $row    = $rowset->current();
        if (!$row) {
            throw new NotFoundException("User not Found");
        }

        return StaticUserFactory::createUser($row->getArrayCopy());
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
        $rowset = $this->userTableGateway->select(['email' => $email]);
        $row    = $rowset->current();
        if (!$row) {
            throw new NotFoundException("User not Found");
        }

        return StaticUserFactory::createUser($row->getArrayCopy());
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
}
