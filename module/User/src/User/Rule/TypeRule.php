<?php

namespace User\Rule;

use Rule\Rule\RuleInterface;
use Rule\Item\RuleItemInterface;
use Rule\Rule\TimesSatisfiedTrait;
use User\UserInterface;

/**
 * A Rule that is satisfied if the check_user matches a type
 */
class TypeRule implements RuleInterface
{
    use TimesSatisfiedTrait;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $providerName;

    /**
     * TypeRule constructor.
     *
     * @param string $type
     * @param string $providerName
     */
    public function __construct(string $type, string $providerName = 'check_user')
    {
        $this->type = $type;
        $this->providerName = $providerName;
    }

    /**
     * @inheritDoc
     */
    public function isSatisfiedBy(RuleItemInterface $event): bool
    {
        $checkUser = $event->getParam($this->providerName);

        if ($checkUser instanceof UserInterface && $checkUser->getType() === $this->type) {
            $this->timesSatisfied++;

            return true;
        }

        return false;
    }
}
