<?php

namespace User\Delegator;

use Application\Exception\NotFoundException;
use Application\Utils\ServiceTrait;
use User\Service\UserService;
use User\Service\UserServiceInterface;
use User\UserInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\EventManager\EventManagerInterface;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Triggers events before the UserService can make calls the to the DB
 */
class UserServiceDelegator implements UserServiceInterface
{
    use ServiceTrait;
    use EventManagerAwareTrait;

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
        $this->setEventManager($events);
        $events->addIdentifiers(array_merge(
            [UserServiceInterface::class, static::class, UserService::class],
            $events->getIdentifiers()
        ));
    }

    /**
     * @param UserInterface $user
     *
     * @return bool|mixed
     * @throws \Exception
     */
    public function createUser(UserInterface $user)
    {
        $event    = new Event('save.new.user', $this->realService, ['user' => $user]);
        $response = $this->getEventManager()->triggerEvent($event);

        if ($response->stopped()) {
            return $response->last();
        }

        try {
            $return = $this->realService->createUser($user);
            $event  = new Event('save.new.user.post', $this->realService, ['user' => $user]);
            $this->getEventManager()->triggerEvent($event);

            return $return;
        } catch (\Exception $createException) {
            $event = new Event(
                'save.new.user.error',
                $this->realService,
                ['user' => $user, 'error' => $createException]
            );

            $this->getEventManager()->triggerEvent($event);

            throw $createException;
        }
    }

    /**
     * @param UserInterface $user
     *
     * @return bool|mixed
     */
    public function updateUser(UserInterface $user)
    {
        $event    = new Event('save.user', $this->realService, ['user' => $user]);
        $response = $this->getEventManager()->triggerEvent($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->updateUser($user);

        $event = new Event('save.user.post', $this->realService, ['user' => $user]);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * Updates the username if the user wants to update his own username
     *
     * @param UserInterface $user
     * @param $username
     *
     * @return mixed|bool
     */
    public function updateUserName(UserInterface $user, $username)
    {
        $event    = new Event('update.user.name', $this->realService, ['user' => $user, 'username' => $username]);
        $response = $this->getEventManager()->triggerEvent($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->updateUserName($user, $username);

        $event->setName('update.user.name.post');
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * Fetches one user from the DB using the id
     *
     * @param $userId
     *
     * @return UserInterface
     * @throws NotFoundException
     */
    public function fetchUser($userId)
    {
        $event    = new Event('fetch.user', $this->realService, ['user_id' => $userId]);
        $response = $this->getEventManager()->triggerEvent($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->fetchUser($userId);
        $event  = new Event('fetch.user.post', $this->realService, ['user_id' => $userId, 'user' => $return]);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * Fetches one user from the DB using the id
     *
     * @param $externalId
     *
     * @return UserInterface
     * @throws NotFoundException
     */
    public function fetchUserByExternalId($externalId)
    {
        $event    = new Event('fetch.user.external', $this->realService, ['external_id' => $externalId]);
        $response = $this->getEventManager()->triggerEvent($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->fetchUserByExternalId($externalId);
        $event  = new Event(
            'fetch.user.external.post',
            $this->realService,
            ['external_id' => $externalId, 'user' => $return]
        );
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * Fetch user from db by username
     *
     * @param $username
     *
     * @return \User\Adult|\User\Child
     * @throws NotFoundException
     */
    public function fetchUserByUsername($username)
    {
        $event    = new Event('fetch.user.username', $this->realService, ['username' => $username]);
        $response = $this->getEventManager()->triggerEvent($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->fetchUserByUsername($username);
        $event  = new Event(
            'fetch.user.username.post',
            $this->realService,
            ['username' => $username, 'user' => $return]
        );
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * Fetches one user from the DB using the email
     *
     * @param $email
     *
     * @return UserInterface
     * @throws NotFoundException
     */
    public function fetchUserByEmail($email)
    {
        $event    = new Event('fetch.user.email', $this->realService, ['email' => $email]);
        $response = $this->getEventManager()->triggerEvent($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->fetchUserByEmail($email);
        $event  = new Event(
            'fetch.user.email.post',
            $this->realService,
            ['email' => $email, 'user' => $return]
        );
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

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
    public function deleteUser(UserInterface $user, $soft = true)
    {
        $event    = new Event('delete.user', $this->realService, ['user' => $user, 'soft' => $soft]);
        $response = $this->getEventManager()->triggerEvent($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->deleteUser($user, $soft);
        $event  = new Event('delete.user.post', $this->realService, ['user' => $user, 'soft' => $soft]);
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }

    /**
     * @param null|PredicateInterface|array $where
     * @param bool $paginate
     * @param null|object $prototype
     *
     * @return HydratingResultSet|DbSelect
     */
    public function fetchAll($where = null, $paginate = true, $prototype = null)
    {
        $where = $this->createWhere($where);
        $event = new Event(
            'fetch.all.users',
            $this->realService,
            ['where' => $where, 'paginate' => $paginate, 'prototype' => $prototype]
        );

        $response = $this->getEventManager()->triggerEvent($event);
        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->fetchAll($where, $paginate, $prototype);
        $event  = new Event(
            'fetch.all.users.post',
            $this->realService,
            ['where' => $where, 'paginate' => $paginate, 'prototype' => $prototype, 'users' => $return]
        );
        $this->getEventManager()->triggerEvent($event);

        return $return;
    }
}
