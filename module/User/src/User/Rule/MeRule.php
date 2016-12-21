<?php

namespace User\Rule;

use Rule\Rule\RuleInterface;
use Rule\Item\RuleItemInterface;
use Rule\Rule\TimesSatisfiedTrait;
use User\UserInterface;

/**
 * A Rule that is satisfied if the check_user is the same as the active_user
 */
class MeRule implements RuleInterface
{
    use \Rule\Rule\TimesSatisfiedTrait;

    /**
     * @inheritDoc
     */
    public function isSatisfiedBy(RuleItemInterface $event): bool
    {
        $activeUser = $event->getParam('active_user');
        $checkUser  = $event->getParam('check_user');

        if (!$activeUser instanceof UserInterface
            || !$checkUser instanceof UserInterface
        ) {
            return false;
        }

        if ($checkUser->getUserId() === $activeUser->getUserId()) {
            $this->timesSatisfied++;

            return true;
        }

        return false;
    }
}
