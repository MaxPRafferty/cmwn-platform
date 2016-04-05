<?php

namespace Security\Authentication;

use Application\Exception\NotFoundException;
use Application\Utils\NoopLoggerAwareTrait;
use Security\ChangePasswordUser;
use Security\Exception\ChangePasswordException;
use Security\GuestUser;
use Security\Service\SecurityOrgService;
use Security\Service\SecurityService;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Exception\RuntimeException;
use Zend\Authentication\Result;
use Zend\Log\LoggerAwareInterface;
use Zend\Validator\StaticValidator;

/**
 * Class AuthAdapter
 * @package Security\Authentication
 */
class AuthAdapter implements AdapterInterface, LoggerAwareInterface
{
    use NoopLoggerAwareTrait;
    
    /**
     * @var SecurityService
     */
    protected $service;

    /**
     * @var string Either the username or the email
     */
    protected $userId;

    /**
     * @var string Either the code or the password
     */
    protected $password;

    /**
     * @var SecurityOrgService
     */
    protected $orgService;

    /**
     * AuthAdapter constructor.
     * @param SecurityService $service
     */
    public function __construct(SecurityService $service, SecurityOrgService $orgService)
    {
        $this->service    = $service;
        $this->orgService = $orgService;
    }

    /**
     * The identifier used for logging in
     *
     * This can either be the user name or the email address
     *
     * @param string $userId user name or email
     * @return $this
     */
    public function setUserIdentifier($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @param $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return Result
     * @throws ChangePasswordException
     */
    public function authenticate()
    {
        if (empty($this->userId)) {
            throw new RuntimeException('a User Identifier is needed in order to authenticate');
        }

        try {
            if (StaticValidator::execute($this->userId, 'EmailAddress')) {
                $user = $this->service->fetchUserByEmail($this->userId);
            } else {
                $user = $this->service->fetchUserByUserName($this->userId);
            }
        } catch (NotFoundException $notFound) {
            $this->getLogger()->alert(
                'Login attempt with non-existent user',
                ['user_id' => $this->userId]
            );
            return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, new GuestUser());
        }

        // Bail early if the password is good
        if ($user->comparePassword($this->password)) {
            $this->getLogger()->notice(
                'Successful user login',
                ['user_id' => $this->userId]
            );
            return new Result(Result::SUCCESS, $user);
        }

        switch ($user->compareCode($this->password)) {
            case $user::CODE_EXPIRED:
                $this->getLogger()->warn(
                    'Code Expired for user',
                    ['user_id' => $this->userId]
                );
                return new Result(Result::FAILURE_UNCATEGORIZED, new GuestUser());

            case $user::CODE_INVALID:
                $this->getLogger()->alert(
                    'Invalid password/code supplied for user',
                    ['user_id' => $this->userId]
                );
                return new Result(Result::FAILURE_CREDENTIAL_INVALID, new GuestUser());

            case $user::CODE_VALID:
                return new Result(
                    Result::SUCCESS,
                    new ChangePasswordUser($user->getArrayCopy())
                );
        }

        $this->getLogger()->emerg('THIS IS THE BAD! THIS IS VERY BAD', ['user_id' => $this->userId]);
        // @codeCoverageIgnoreStart
        // Hard to get here unless a new code status is added
        return new Result(Result::FAILURE_IDENTITY_AMBIGUOUS, new GuestUser());
    }
}
