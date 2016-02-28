<?php

namespace User\Service;

use Application\Exception\NotFoundException;
use Ramsey\Uuid\Uuid;
use User\StaticUserFactory;
use User\UserHydrator;
use User\UserInterface;
use Zend\Db\ResultSet\HydratingResultSet;
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
     * @param null|Where|array $where
     * @param bool $paginate
     * @param null|object $prototype
     * @return HydratingResultSet|DbSelect
     */
    public function fetchAll($where = null, $paginate = true, $prototype = null)
    {
        $where     = !$where instanceof Where ? new Where($where) : $where;
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
     * Saves a user
     *
     * If the user id is null, then a new user is created
     *
     * @param UserInterface $user
     * @return bool
     * @throws NotFoundException
     */
    public function saveUser(UserInterface $user)
    {
        $new = empty($user->getUserId());
        $user->setUpdated(new \DateTime());
        $data = $user->getArrayCopy();

        $data['meta'] = Json::encode($data['meta']);

        unset($data['password']);
        unset($data['deleted']);

        if ($new) {
            $user->setCreated(new \DateTime());
            $user->setUserId(Uuid::uuid1());

            $data['user_id'] = $user->getUserId();
            $data['created'] = $user->getCreated()->format(\DateTime::ISO8601);

            $this->userTableGateway->insert($data);
            return true;
        }

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
