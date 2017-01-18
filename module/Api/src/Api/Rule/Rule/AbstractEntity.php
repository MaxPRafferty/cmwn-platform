<?php

namespace Api\Rule\Rule;

use Rule\Item\RuleItemInterface;
use Rule\Rule\RuleInterface;
use Security\Authorization\Rbac;

/**
 * Class AbstractEntity
 * @package Api\Rule\Rule
 */
abstract class AbstractEntity implements RuleInterface
{
    /**
     * @var Rbac
     */
    protected $rbac;

    /**
     * @var array
     */
    protected $permissions;

    /**
     * AbstractEntity constructor.
     * @param Rbac $rbac
     * @param array $permissions
     */
    public function __construct(Rbac $rbac, array $permissions = [])
    {
        $this->rbac = $rbac;
        $this->permissions = $permissions;
    }

    /**@inheritdoc*/
    public function isSatisfiedBy(RuleItemInterface $item): bool
    {
        $role = $item->getParam($this->getProviderName());

        if (!$role) {
            return false;
        }
    }

    /**
     * @return string
     */
    abstract public function getProviderName() : string;
}
