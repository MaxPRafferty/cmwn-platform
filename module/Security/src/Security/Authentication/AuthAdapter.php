<?php

namespace Security\Authentication;

use Application\Exception\NotFoundException;
use Application\Utils\NoopLoggerAwareTrait;
use Security\ChangePasswordUser;
use Security\Exception\ChangePasswordException;
use Security\GuestUser;
use Security\SecurityUserInterface;
use Security\Service\SecurityServiceInterface;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Exception\RuntimeException;
use Zend\Authentication\Result;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;
use Zend\Log\LoggerAwareInterface;
use Zend\Validator\StaticValidator;

/**
 * Adapter for logging in
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AuthAdapter implements AdapterInterface, LoggerAwareInterface
{
    use NoopLoggerAwareTrait;

    /**
     * @var SecurityServiceInterface
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
     * @var EventManagerInterface
     */
    protected $events;

    /**
     * AuthAdapter constructor.
     *
     * @param SecurityServiceInterface $service
     * @param EventManagerInterface $events
     */
    public function __construct(SecurityServiceInterface $service, EventManagerInterface $events)
    {
        $this->service = $service;
        $this->events  = $events;
    }

    /**
     * The identifier used for logging in
     *
     * This can either be the user name or the email address
     *
     * @param string $userId user name or email
     *
     * @return $this
     */
    public function setUserIdentifier($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @param $password
     *
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
            $this->trigger('login.not.found');
            $this->getLogger()->alert(
                'Login attempt with non-existent user',
                ['user_id' => $this->userId]
            );

            return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, new GuestUser());
        }

        if ($user->isDeleted()) {
            $this->trigger('login.not.found', $user);
            $this->getLogger()->warn(
                'Deleted user attempted to login',
                ['user_id' => $this->userId]
            );

            return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, new GuestUser());
        }

        // Bail early if the password is good
        if ($user->comparePassword($this->password)) {
            $this->trigger('login.success', $user);
            $this->getLogger()->notice(
                'Successful user login',
                ['user_id' => $this->userId]
            );

            return new Result(Result::SUCCESS, $user);
        }

        switch ($user->compareCode($this->password)) {
            case SecurityUserInterface::CODE_EXPIRED:
                $this->trigger('login.expired', $user);
                $this->getLogger()->warn(
                    'Code Expired for user',
                    ['user_id' => $this->userId]
                );

                return new Result(Result::FAILURE_UNCATEGORIZED, new GuestUser());

            case SecurityUserInterface::CODE_INVALID:
                $this->trigger('login.invalid', $user);
                $this->getLogger()->warn(
                    'Invalid password/code supplied for user',
                    ['user_id' => $this->userId]
                );

                return new Result(Result::FAILURE_CREDENTIAL_INVALID, new GuestUser());

            case SecurityUserInterface::CODE_VALID:
                $this->trigger('login.with.code', $user);
                $this->getLogger()->notice(
                    'User Logged in with correct code',
                    ['user_id' => $this->userId]
                );

                return new Result(
                    Result::SUCCESS,
                    new ChangePasswordUser($user->getArrayCopy())
                );
        }

        // @codeCoverageIgnoreStart
        // Hard to get here unless a new code status is added
        $this->getLogger()->emerg('THIS IS THE BAD! SHOW THEM THE BAD', ['user_id' => $this->userId]);

        return new Result(Result::FAILURE_IDENTITY_AMBIGUOUS, new GuestUser());
    }

    /**
     * @param string $eventName
     * @param SecurityUserInterface|null $user
     */
    protected function trigger(string $eventName, SecurityUserInterface $user = null)
    {
        $this->events->triggerEvent(new Event($eventName, $user));
    }
}
