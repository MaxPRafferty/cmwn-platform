<?php

namespace Security\Authorization\Assertions;

use Security\Authorization\AssertionInterface;
use Security\Authorization\AssertionTrait;
use Security\Service\SecurityGroupServiceInterface;
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
     * @var SecurityGroupServiceInterface
     */
    protected $securityGroupService;

    /**
     * UserAssertion constructor.
     *
     * @param SecurityGroupServiceInterface $securityGroupService
     */
    public function __construct(SecurityGroupServiceInterface $securityGroupService)
    {
        $this->securityGroupService = $securityGroupService;
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
            $role = $this->securityGroupService->fetchRelationshipRole($this->activeUser, $this->requestedUser);
        }

        $role .= '.' . strtolower($this->activeUser->getType());
        //attach requested user type to permission
        foreach ($this->permission as $permission) {
            if ($rbac->isGranted($role, $permission)) {
                return true;
            }
        }

        return false;
    }
}
