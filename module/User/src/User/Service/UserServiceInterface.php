<?php

namespace User\Service;

use Application\Exception\NotFoundException;
use User\UserInterface;
use Zend\Paginator\Adapter\AdapterInterface;

/**
 * An interface that defines the UserService
 */
interface UserServiceInterface
{
    /**
     * Fetches one user from the DB using the id
     *
     * @param string $userId
     * @param UserInterface $prototype
     *
     * @return UserInterface
     */
    public function fetchUser(string $userId, UserInterface $prototype = null): UserInterface;

    /**
     * Fetches one user from the DB using the external id
     *
     * @param $externalId
     *
     * @return UserInterface
     * @throws NotFoundException
     */
    public function fetchUserByExternalId(string $externalId): UserInterface;

    /**
     * Fetch user from db by username
     *
     * @param $username
     *
     * @return UserInterface
     * @throws NotFoundException
     */
    public function fetchUserByUsername(string $username): UserInterface;

    /**
     * Fetches one user from the DB using the email
     *
     * @param $email
     *
     * @return UserInterface
     * @throws NotFoundException
     */
    public function fetchUserByEmail(string $email): UserInterface;

    /**
     * Deletes a user from the database
     *
     * Soft deletes unless soft is false
     *
     * @param UserInterface $user
     * @param bool $soft
     *
     * @return bool
     */
    public function deleteUser(UserInterface $user, bool $soft = true): bool;

    /**
     * Fethes all users
     *
     * @param null $where
     * @param UserInterface|null $prototype
     *
     * @return AdapterInterface
     */
    public function fetchAll($where = null, UserInterface $prototype = null): AdapterInterface;

    /**
     * Create a new user
     *
     * A User Id will be auto generated
     *
     * @param UserInterface $user
     *
     * @return bool
     */
    public function createUser(UserInterface $user): bool;

    /**
     * Saves an Existing user
     *
     * @param UserInterface $user
     *
     * @return bool
     * @throws NotFoundException
     */
    public function updateUser(UserInterface $user): bool;

    /**
     * @param UserInterface $user
     * @param string $username
     *
     * @return bool
     */
    public function updateUserName(UserInterface $user, string $username): bool;
}
