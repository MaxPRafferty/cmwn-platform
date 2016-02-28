<?php

namespace User\Delegator;

use Application\Exception\NotFoundException;
use User\Service\UserService;
use User\Service\UserServiceInterface;
use User\UserInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\Predicate\PredicateInterface;
use Zend\Db\Sql\Where;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Class UserServiceDelegator
 * @package User\Delegator
 */
class UserServiceDelegator implements UserServiceInterface, EventManagerAwareInterface
{
    use EventManagerAwareTrait;

    /**
     * @var UserService
     */
    protected $realService;

    public function __construct(UserService $service)
    {
        $this->realService = $service;
    }

    protected function attachDefaultListeners()
    {
        $this->getEventManager()->attach(new CheckUserListener());
        $this->getEventManager()->attach(new HideDeletedUsersListener());
    }


    /**
     * Saves a user
     *
     * If the user id is null, then a new user is created
     *
     * @param UserInterface $user
     * @return bool
     * @throws NotFoundException
     */
    public function saveUser(UserInterface $user)
    {
        $event    = new Event('save.user', $this->realService, ['user' => $user]);
        $response = $this->getEventManager()->trigger($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->saveUser($user);

        $event    = new Event('save.user.post', $this->realService, ['user' => $user]);
        $this->getEventManager()->trigger($event);

        return $return;

    }

    /**
     * Fetches one user from the DB using the id
     *
     * @param $userId
     * @return UserInterface
     * @throws NotFoundException
     */
    public function fetchUser($userId)
    {
        $event    = new Event('fetch.user', $this->realService, ['user_id' => $userId]);
        $response = $this->getEventManager()->trigger($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->fetchUser($userId);
        $event    = new Event('fetch.user.post', $this->realService, ['user_id' => $userId, 'user' => $return]);
        $this->getEventManager()->trigger($event);
        return $return;
    }

    /**
     * Deletes a user from the database
     *
     * Soft deletes unless soft is false
     *
     * @param UserInterface $user
     * @param bool $soft
     * @return bool
     */
    public function deleteUser(UserInterface $user, $soft = true)
    {
        $event    = new Event('delete.user', $this->realService, ['user' => $user, 'soft' => $soft]);
        $response = $this->getEventManager()->trigger($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->deleteUser($user, $soft);
        $event  = new Event('delete.user.post', $this->realService, ['user' => $user, 'soft' => $soft]);
        $this->getEventManager()->trigger($event);
        return $return;
    }

    /**
     * @param null|PredicateInterface|array $where
     * @param bool $paginate
     * @param null|object $prototype
     * @return HydratingResultSet|DbSelect
     */
    public function fetchAll($where = null, $paginate = true, $prototype = null)
    {
        $where    = !$where instanceof PredicateInterface ? new Where($where) : $where;
        $event    = new Event(
            'fetch.all.users',
            $this->realService,
            ['where' => $where, 'paginate' => $paginate, 'prototype' => $prototype]
        );

        $response = $this->getEventManager()->trigger($event);
        if ($response->stopped()) {
            return $response->last();
        }

        $return   = $this->realService->fetchAll($where, $paginate, $prototype);
        $event    = new Event(
            'fetch.all.users.post',
            $this->realService,
            ['where' => $where, 'paginate' => $paginate, 'prototype' => $prototype, 'users' => $return]
        );
        $this->getEventManager()->trigger($event);

        return $return;
    }
}
