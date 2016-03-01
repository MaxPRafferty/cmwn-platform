<?php

namespace User\Service;

use Application\Exception\NotFoundException;
use User\UserInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Paginator\Adapter\DbSelect;

interface UserServiceInterface
{
    /**
     * Fetches one user from the DB using the id
     *
     * @param $userId
     * @return UserInterface
     * @throws NotFoundException
     */
    public function fetchUser($userId);

    /**
     * Deletes a user from the database
     *
     * Soft deletes unless soft is false
     *
     * @param UserInterface $user
     * @param bool $soft
     * @return bool
     */
    public function deleteUser(UserInterface $user, $soft = true);

    /**
     * @param null|PredicateInterface|array $where
     * @param bool $paginate
     * @param null|object $prototype
     * @return HydratingResultSet|DbSelect
     */
    public function fetchAll($where = null, $paginate = true, $prototype = null);

    /**
     * Create a new user
     *
     * A User Id will be auto generated
     *
     * @param UserInterface $user
     * @return bool
     */
    public function createUser(UserInterface $user);

    /**
     * Saves an Existing user
     *
     * @param UserInterface $user
     * @return bool
     * @throws NotFoundException
     */
    public function updateUser(UserInterface $user);
}
