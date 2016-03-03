<?php

namespace User\Delegator;

use Application\Exception\NotFoundException;
use Application\Utils\HideDeletedEntitiesListener;
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
        $hideListener = new HideDeletedEntitiesListener(['fetch.all.users'], ['fetch.user.post']);
        $hideListener->setEntityParamKey('user');

        $this->getEventManager()->attach(new CheckUserListener());
        $this->getEventManager()->attach($hideListener);
    }

    public function createUser(UserInterface $user)
    {
        $event    = new Event('save.new.user', $this->realService, ['user' => $user]);
        $response = $this->getEventManager()->trigger($event);

        if ($response->stopped()) {
            return $response->last();
        }

        try {
            $return = $this->realService->createUser($user);
            $event    = new Event('save.new.user.post', $this->realService, ['user' => $user]);
            $this->getEventManager()->trigger($event);

            return $return;
        } catch (\Exception $createException) {
            $event    = new Event(
                'save.new.user.error',
                $this->realService,
                ['user' => $user, 'error' => $createException]
            );

            $this->getEventManager()->trigger($event);

            return false;
        }
    }

    public function updateUser(UserInterface $user)
    {
        $event    = new Event('save.user', $this->realService, ['user' => $user]);
        $response = $this->getEventManager()->trigger($event);

        if ($response->stopped()) {
            return $response->last();
        }

        $return = $this->realService->updateUser($user);

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
