<?php

namespace Security\Authorization\Assertions;

use Group\Service\UserGroupServiceInterface;
use Security\Authorization\AssertionInterface;
use Security\Authorization\AssertionTrait;
use User\UserInterface;
use Zend\Permissions\Rbac\Rbac;

/**
 * Class UserAssertion
 */
class UserAssertion implements AssertionInterface
{
    use AssertionTrait;

    /**
     * @var UserInterface
     */
    protected $activeUser;

    /**
     * @var UserInterface
     */
    protected $requestedUser;

    /**
     * @var UserGroupServiceInterface
     */
    protected $userGroupService;

    /**
     * UserAssertion constructor.
     *
     * @param UserGroupServiceInterface $userGroupService
     */
    public function __construct(UserGroupServiceInterface $userGroupService)
    {
        $this->userGroupService = $userGroupService;
    }

    /**
     * @param UserInterface $user
     */
    public function setActiveUser(UserInterface $user)
    {
        $this->activeUser = $user;
    }

    /**
     * @param UserInterface $user
     */
    public function setRequestedUser(UserInterface $user)
    {
        $this->requestedUser = $user;
    }

    /**
     * Assertion method - must return a boolean.
     *
     * @param  Rbac $rbac
     * @return bool
     */
    public function assert(Rbac $rbac)
    {
        if (!$this->requestedUser) {
            return false;
        }

        $role = ($this->requestedUser->getUserId() === $this->activeUser->getUserId()) ? 'me' : 'guest';
        if ($this->requestedUser->getUserId() !== $this->activeUser->getUserId()) {
            $role = $this->userGroupService->fetchRoleToUser($this->activeUser, $this->requestedUser);
        }

        //attach requested user type to permission
        $permission = $this->permission . '.' . strtolower($this->requestedUser->getType());
        return $rbac->isGranted($role, $permission);
    }
}
