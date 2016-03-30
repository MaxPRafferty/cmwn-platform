<?php

namespace Security\Authorization\Assertions;

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
    protected $loggedInUser;

    /**
     * @var UserInterface
     */
    protected $checkUser;

    /**
     * UserAssertion constructor.
     * @param UserInterface $user
     */
    public function __construct(UserInterface $user)
    {
        $this->loggedInUser = $user;
    }

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user)
    {
        $this->checkUser = $user;
    }

    /**
     * Assertion method - must return a boolean.
     *
     * @param  Rbac $rbac
     * @return bool
     */
    public function assert(Rbac $rbac)
    {
        if (!$this->checkUser) {
            return false;
        }

        if ($this->checkUser->getUserId() === $this->loggedInUser->getUserId()) {
            return true;
        }

        return $rbac->isGranted($this->role, $this->permission);
    }
}
