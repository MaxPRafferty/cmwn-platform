<?php

namespace Security\Rule\Rule;

use Rule\Item\RuleItemInterface;
use Rule\Rule\RuleInterface;
use Rule\Rule\TimesSatisfiedTrait;
use Security\SecurityUserInterface;
use Zend\Permissions\Rbac\Rbac;

/**
 * A Rule that is satisfied if a role has a permission
 */
class HasPermission implements RuleInterface
{
    use TimesSatisfiedTrait;

    /**
     * @var Rbac
     */
    protected $rbac;

    /**
     * @var string
     */
    protected $roleProvider;

    /**
     * @var string
     */
    protected $neededPermission;

    /**
     * HasPermission constructor.
     *
     * @param Rbac $rbac
     * @param string $neededPermission
     * @param string $roleProvider
     */
    public function __construct(Rbac $rbac, string $neededPermission, string $roleProvider = 'active_role')
    {
        $this->rbac             = $rbac;
        $this->roleProvider     = $roleProvider;
        $this->neededPermission = $neededPermission;
    }

    /**
     * @inheritDoc
     */
    public function isSatisfiedBy(RuleItemInterface $item): bool
    {
        $role = $item->getParam($this->roleProvider, SecurityUserInterface::ROLE_GUEST);
        if (!$this->rbac->isGranted($role, $this->neededPermission)) {
            return false;
        }

        $this->timesSatisfied++;

        return true;
    }
}
