<?php

namespace Group\Delegator;

use Group\GroupInterface;
use Group\Service\UserGroupService;
use Group\Service\UserGroupServiceInterface;
use User\UserInterface;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * Class UserGroupServiceDelegator
 * @package Group\Delegator
 */
class UserGroupServiceDelegator implements UserGroupServiceInterface, EventManagerAwareInterface
{
    use EventManagerAwareTrait;

    /**
     * @var UserGroupService
     */
    protected $realService;

    /**
     * UserGroupServiceDelegator constructor.
     * @param UserGroupService $realService
     */
    public function __construct(UserGroupService $realService)
    {
        $this->realService = $realService;
    }

    /**
     * Attaches a user to a group
     *
     * @param GroupInterface $group
     * @param UserInterface $user
     * @param RoleInterface|string $role
     * @return bool
     * @throws \RuntimeException
     */
    public function attachUserToGroup(GroupInterface $group, UserInterface $user, $role)
    {
        $eventParams = ['group' => $group, 'user' => $user, 'role' => $role];
        $event       = new Event('attach.user', $this->realService, $eventParams);
        if ($this->getEventManager()->trigger($event)->stopped()) {
            return false;
        }

        try {
            $return = $this->realService->attachUserToGroup($group, $user, $role);
        } catch (\Exception $attachException) {
            $eventParams['exception'] = $attachException;
            $event                    = new Event('attach.user.error', $this->realService, $eventParams);
            $this->getEventManager()->trigger($event);

            return false;
        }

        $event = new Event('attach.user.post', $this->realService, $eventParams);
        $this->getEventManager()->trigger($event);
        return $return;
    }

    /**
     * Detaches a user from a group
     *
     * @param GroupInterface $group
     * @param UserInterface $user
     * @return bool
     */
    public function detachUserFromGroup(GroupInterface $group, UserInterface $user)
    {
        $eventParams = ['group' => $group, 'user' => $user];
        $event       = new Event('detach.user', $this->realService, $eventParams);
        if ($this->getEventManager()->trigger($event)->stopped()) {
            return false;
        }

        try {
            $return = $this->realService->detachUserFromGroup($group, $user);
        } catch (\Exception $attachException) {
            $eventParams['exception'] = $attachException;
            $event                    = new Event('detach.user.error', $this->realService, $eventParams);
            $this->getEventManager()->trigger($event);

            return false;
        }

        $event = new Event('detach.user.post', $this->realService, $eventParams);
        $this->getEventManager()->trigger($event);
        return $return;
    }
}
