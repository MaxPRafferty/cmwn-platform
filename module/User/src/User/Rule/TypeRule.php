<?php

namespace User\Rule;

use Rule\RuleInterface;
use Rule\RuleItemInterface;
use Rule\TimesSatisfiedTrait;
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
     * TypeRule constructor.
     *
     * @param string $type
     */
    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * @inheritDoc
     */
    public function isSatisfiedBy(RuleItemInterface $event): bool
    {
        $checkUser = $event->getParam('check_user');

        if ($checkUser instanceof UserInterface && $checkUser->getType() === $this->type) {
            $this->timesSatisfied++;

            return true;
        }

        return false;
    }
}
