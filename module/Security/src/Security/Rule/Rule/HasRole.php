<?php

namespace Security\Rule\Rule;

use Rule\Item\RuleItemInterface;
use Rule\Rule\RuleInterface;
use Rule\Rule\TimesSatisfiedTrait;
use Rule\Utils\ProviderTypeTrait;
use Security\GuestUser;
use Security\SecurityUserInterface;

/**
 * Class HasRole
 */
class HasRole implements RuleInterface
{
    use TimesSatisfiedTrait;
    use ProviderTypeTrait;

    /**
     * @var string
     */
    protected $userProvider;

    /**
     * @var string
     */
    protected $expectedRole;

    /**
     * @inheritDoc
     */
    public function __construct(string $expectedRole, string $roleProvider = 'active_user')
    {
        $this->userProvider = $roleProvider;
        $this->expectedRole = $expectedRole;
    }

    /**
     * @inheritDoc
     */
    public function isSatisfiedBy(RuleItemInterface $item): bool
    {
        /** @var SecurityUserInterface $user */
        $user = $item->getParam($this->userProvider, new GuestUser());
        static::checkValueType($user, SecurityUserInterface::class);

        if ($user->getRole() !== $this->expectedRole) {
            return false;
        };

        $this->timesSatisfied++;
        return true;
    }
}
