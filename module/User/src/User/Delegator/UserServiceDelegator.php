<?php

namespace User\Delegator;

use Application\Utils\ServiceTrait;
use User\Service\UserService;
use User\Service\UserServiceInterface;
use User\UserInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;
use Zend\Paginator\Adapter\AdapterInterface;

/**
 * Triggers events before the UserService can make calls the to the DB
 */
class UserServiceDelegator implements UserServiceInterface
{
    use ServiceTrait;

    /**
     * @var EventManagerInterface
     */
    protected $events;

    /**
     * @var UserService
     */
    protected $realService;

    /**
     * UserServiceDelegator constructor.
     *
     * @param UserService $service
     * @param EventManagerInterface $events
     */
    public function __construct(UserService $service, EventManagerInterface $events)
    {
        $this->realService = $service;
        $this->events      = $events;
        $events->addIdentifiers(array_merge(
            [UserServiceInterface::class, static::class, UserService::class],
            $events->getIdentifiers()
        ));
    }

    /**
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        return $this->events;
    }

    /**
     * @inheritdoc
     */
    public function createUser(UserInterface $user): bool
    {
        $event = new Event(
            'save.new.user',
            $this->realService,
            ['user' => $user]
        );

        try {
            $response = $this->getEventManager()->triggerEvent($event);
            if ($response->stopped()) {
                return $response->last();
            }

            $return = $this->realService->createUser($user);
        } catch (\Throwable $exception) {
            $event->setName('save.new.user.error');
            $event->setParam('error', $exception);
            $this->getEventManager()->triggerEvent($event);
            throw $exception;
        }

        $event->setName('save.new.user.post');
        $event->setParam('result', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function updateUser(UserInterface $user): bool
    {
        $event = new Event('save.user', $this->realService, ['user' => $user]);
        try {
            $response = $this->getEventManager()->triggerEvent($event);

            if ($response->stopped()) {
                return $response->last();
            }

            $return = $this->realService->updateUser($user);
        } catch (\Throwable $exception) {
            $event->setName('save.user.error');
            $event->setParam('error', $exception);
            $this->getEventManager()->triggerEvent($event);

            throw $exception;
        }

        $event->setName('save.user.post');
        $event->setParam('result', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function updateUserName(UserInterface $user, string $username): bool
    {
        $event = new Event(
            'update.user.name',
            $this->realService,
            ['user' => $user, 'username' => $username]
        );

        try {
            $response = $this->getEventManager()->triggerEvent($event);

            if ($response->stopped()) {
                return $response->last();
            }

            $return = $this->realService->updateUserName($user, $username);
        } catch (\Throwable $exception) {
            $event->setName('update.user.name.error');
            $event->setParam('exception', $exception);
            $this->getEventManager()->triggerEvent($event);

            throw $exception;
        }

        $event->setName('update.user.name.post');
        $event->setParam('result', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function fetchUser(string $userId, UserInterface $prototype = null): UserInterface
    {
        $event = new Event(
            'fetch.user',
            $this->realService,
            ['user_id' => $userId]
        );

        try {
            $response = $this->getEventManager()->triggerEvent($event);

            if ($response->stopped()) {
                return $response->last();
            }

            $return = $this->realService->fetchUser($userId, $prototype);
        } catch (\Throwable $exception) {
            $event->setName('fetch.user.error');
            $event->setParam('exception', $exception);
            $this->getEventManager()->triggerEvent($event);

            throw $exception;
        }

        $event->setName('fetch.user.post');
        $event->setParam('user', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function fetchUserByExternalId(string $externalId, UserInterface $prototype = null): UserInterface
    {
        $event = new Event(
            'fetch.user.external',
            $this->realService,
            ['external_id' => $externalId]
        );

        try {
            $response = $this->getEventManager()->triggerEvent($event);

            if ($response->stopped()) {
                return $response->last();
            }

            $return = $this->realService->fetchUserByExternalId($externalId);
        } catch (\Throwable $exception) {
            $event->setName('fetch.user.external.error');
            $event->setParam('exception', $exception);
            $this->getEventManager()->triggerEvent($event);

            throw $exception;
        }

        $event->setName('fetch.user.external.post');
        $event->setParam('user', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function fetchUserByUsername(string $username, UserInterface $prototype = null): UserInterface
    {
        $event = new Event(
            'fetch.user.username',
            $this->realService,
            ['username' => $username]
        );

        try {
            $response = $this->getEventManager()->triggerEvent($event);

            if ($response->stopped()) {
                return $response->last();
            }

            $return = $this->realService->fetchUserByUsername($username);
        } catch (\Throwable $exception) {
            $event->setName('fetch.user.username.error');
            $event->setParam('exception', $exception);
            $this->getEventManager()->triggerEvent($event);

            throw $exception;
        }

        $event->setName('fetch.user.username.post');
        $event->setParam('user', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function fetchUserByEmail(string $email, UserInterface $prototype = null): UserInterface
    {
        $event = new Event(
            'fetch.user.email',
            $this->realService,
            ['email' => $email]
        );

        try {
            $response = $this->getEventManager()->triggerEvent($event);

            if ($response->stopped()) {
                return $response->last();
            }

            $return = $this->realService->fetchUserByEmail($email);
        } catch (\Throwable $exception) {
            $event->setName('fetch.user.email.error');
            $event->setParam('exception', $exception);
            $this->getEventManager()->triggerEvent($event);

            throw $exception;
        }

        $event->setName('fetch.user.email.post');
        $event->setParam('user', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function deleteUser(UserInterface $user, bool $soft = true): bool
    {
        $event = new Event(
            'delete.user',
            $this->realService,
            ['user' => $user, 'soft' => $soft]
        );

        try {
            $response = $this->getEventManager()->triggerEvent($event);

            if ($response->stopped()) {
                return $response->last();
            }

            $return = $this->realService->deleteUser($user, $soft);
        } catch (\Throwable $exception) {
            $event->setName('delete.user.error');
            $event->setParam('exception', $exception);
            $this->getEventManager()->triggerEvent($event);

            throw $exception;
        }

        $event->setName('delete.user.post');
        $event->setParam('result', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @inheritdoc
     */
    public function fetchAll($where = null, UserInterface $prototype = null): AdapterInterface
    {
        $where = $this->createWhere($where);
        $event = new Event(
            'fetch.all.users',
            $this->realService,
            ['where' => $where, 'prototype' => $prototype]
        );

        try {
            $response = $this->getEventManager()->triggerEvent($event);
            if ($response->stopped()) {
                return $response->last();
            }

            $return = $this->realService->fetchAll($where, $prototype);
        } catch (\Throwable $exception) {
            $event->setName('fetch.all.users.error');
            $event->setParam('exception', $exception);
            $this->getEventManager()->triggerEvent($event);

            throw $exception;
        }

        $event->setName('fetch.all.users.post');
        $event->setParam('users', $return);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }
}
