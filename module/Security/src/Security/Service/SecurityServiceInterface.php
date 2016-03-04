<?php

namespace Security\Service;

use Application\Exception\NotFoundException;
use Security\SecurityUser;

/**
 * Interface SecurityServiceInterface
 * @package Security\Service
 */
interface SecurityServiceInterface
{
    /**
     * Encrypts the password
     *
     * @param $password
     * @return mixed
     */
    public static function encryptPassword($password);

    /**
     * Fetches a user by the email
     *
     * @param $email
     * @return SecurityUser
     * @throws NotFoundException
     */
    public function fetchUserByEmail($email);

    /**
     * Fetches a user by the user name
     *
     * @param $username
     * @return SecurityUser
     * @throws NotFoundException
     */
    public function fetchUserByUserName($username);

    /**
     * Saves the encrypted password to a user
     *
     * @param $user
     * @param $password
     * @return bool
     */
    public function savePasswordToUser($user, $password);

    /**
     * Sets the user as a super admin
     *
     * @param $user
     * @param bool $super
     */
    public function setSuper($user, $super = true);
}
