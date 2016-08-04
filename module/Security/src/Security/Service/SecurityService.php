<?php

namespace Security\Service;

use Application\Exception\NotFoundException;
use Application\Utils\NoopLoggerAwareTrait;
use Security\SecurityUser;
use User\UserInterface;
use Zend\Db\TableGateway\TableGateway;
use Zend\Log\LoggerAwareInterface;

/**
 * Class SecurityService
 */
class SecurityService implements SecurityServiceInterface, LoggerAwareInterface
{
    use NoopLoggerAwareTrait;

    /**
     * @var TableGateway
     */
    protected $gateway;

    /**
     * SecurityService constructor.
     * @param TableGateway $gateway
     */
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
        $rowSet = $this->gateway->select(['email' => $email]);
        $row    = $rowSet->current();
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
        $rowSet = $this->gateway->select(['username' => $username]);
        $row    = $rowSet->current();
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

        $this->getLogger()->notice('Saving new password to user: ' . $userId);

        $this->gateway->update(
            ['password' => static::encryptPassword($password), 'code' => null, 'code_expires' => null],
            ['user_id'  => $userId]
        );

        return true;
    }

    /**
     * Sets the user as a super admin
     *
     * @param $user
     * @param bool $super
     * @return bool
     */
    public function setSuper($user, $super = true)
    {
        $bit    = $super ? 1 : 0;
        $userId = $user instanceof UserInterface ? $user->getUserId() : $user;

        $this->getLogger()->notice('Setting user to be a super user: ' . $userId);

        $this->gateway->update(
            ['super'   => $bit],
            ['user_id' => $userId]
        );

        return true;
    }

    /**
     * Saves the temp code to a user
     *
     * @param $code
     * @param UserInterface $user
     * @return bool
     */
    public function saveCodeToUser($code, $user)
    {
        $userId  = $user instanceof UserInterface ? $user->getUserId() : $user;
        $expires = new \DateTime('+3 days');
        $this->gateway->update(
            ['code'    => $code, 'code_expires' => (string) $expires->format("Y-m-d H:i:s")],
            ['user_id' => $userId]
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
