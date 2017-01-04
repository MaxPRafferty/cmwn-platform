<?php

namespace Security\Authentication;

use Security\ChangePasswordUser;
use Security\Exception\ChangePasswordException;
use Security\GuestUser;
use Security\SecurityUserInterface;
use Zend\Authentication\Adapter;
use Zend\Authentication\AuthenticationService as ZfAuthService;
use Zend\Authentication\Result;
use Zend\Authentication\Storage;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\EventManager\EventManagerInterface;

/**
 * Authentication service that will always return a user type for getIdentity
 */
class AuthenticationService extends ZfAuthService
{
    use EventManagerAwareTrait;

    /**
     * @var string
     */
    protected $eventIdentifier = self::class;

    /**
     * @inheritDoc
     */
    public function __construct(
        EventManagerInterface $events,
        Storage\StorageInterface $storage = null,
        Adapter\AdapterInterface $adapter = null
    ) {
        parent::__construct($storage, $adapter);
        $this->setEventManager($events);
    }

    /**
     * Returns the identity from storage or GuestUser if no identity is available
     *
     * If the user needs to change their password, than an exception is thrown
     *
     * @throws ChangePasswordException
     * @return SecurityUserInterface
     */
    public function getIdentity()
    {
        $identity = parent::getIdentity();
        if ($identity instanceof ChangePasswordUser) {
            throw new ChangePasswordException($identity);
        }

        return !$identity instanceof SecurityUserInterface
            ? new GuestUser()
            : $identity;
    }

    /**
     * @inheritDoc
     */
    public function authenticate(Adapter\AdapterInterface $adapter = null)
    {
        $result = parent::authenticate($adapter);
        $event  = new Event();
        switch ($result->getCode()) {
            case Result::FAILURE_IDENTITY_NOT_FOUND:
                $event->setName('login.not.found');
                break;

            case Result::FAILURE_CREDENTIAL_INVALID:
                $event->setName('login.invalid');
                break;

            case Result::FAILURE_UNCATEGORIZED:
                $event->setName('login.expired');
                break;

            case Result::SUCCESS:
                $event->setName('login.success');
                break;

            case Result::FAILURE_IDENTITY_AMBIGUOUS:
            case Result::FAILURE:
                $event->setName('login.fatal.error');
                break;
        }

        $event->setTarget($result->getIdentity());
        $this->events->triggerEvent($event);

        return $result;
    }
}
