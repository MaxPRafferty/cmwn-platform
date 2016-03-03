<?php

namespace Security\Service;

use Application\Exception\NotFoundException;
use Security\SecurityUser;
use User\UserInterface;
use Zend\Db\TableGateway\TableGateway;

class SecurityService implements SecurityServiceInterface
{
    /**
     * @var TableGateway
     */
    protected $gateway;

    public function __construct(TableGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * Fetches a user by the email
     *
     * @param $email
     * @return SecurityUser
     * @throws NotFoundException
     */
    public function fetchUserByEmail($email)
    {
        $rowset = $this->gateway->select(['email' => $email]);
        $row    = $rowset->current();
        if (!$row) {
            throw new NotFoundException("User not Found");
        }

        return new SecurityUser($row->getArrayCopy());
    }

    /**
     * Fetches a user by the user name
     *
     * @param $username
     * @return SecurityUser
     * @throws NotFoundException
     */
    public function fetchUserByUserName($username)
    {
        $rowset = $this->gateway->select(['username' => $username]);
        $row    = $rowset->current();
        if (!$row) {
            throw new NotFoundException("User not Found");
        }

        return new SecurityUser($row->getArrayCopy());
    }

    /**
     * Saves the encrypted password to a user
     *
     * @param $user
     * @param $password
     * @return bool
     */
    public function savePasswordToUser($user, $password)
    {
        $userId = $user instanceof UserInterface ? $user->getUserId() : $user;

        $this->gateway->update(
            ['password' => static::encryptPassword($password)],
            ['user_id'  => $userId]
        );

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
