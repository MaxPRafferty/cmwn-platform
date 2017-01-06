<?php

namespace User\Rule;

use Rule\Rule\Date\DateBetweenRule;
use Rule\Rule\RuleInterface;
use Rule\Item\RuleItemInterface;
use Rule\Rule\TimesSatisfiedTrait;
use User\UserInterface;

/**
 * A Rule that is satisfied if the current date is the check_user 's birthday
 */
class BirthdayRule implements RuleInterface
{
    use TimesSatisfiedTrait;

    /**
     * @inheritDoc
     */
    public function isSatisfiedBy(RuleItemInterface $event): bool
    {
        $checkUser = $event->getParam('check_user');
        if (!$checkUser instanceof UserInterface) {
            return false;
        }

        $startDate = clone $checkUser->getBirthdate();
        $endDate   = clone $checkUser->getBirthdate();

        $startDate->setTime(0, 0, 0);
        $endDate->setTime(23, 59, 59); // Leap Seconds who needs them
        $between = new DateBetweenRule($startDate, $endDate);

        if ($between->isSatisfiedBy($event)) {
            $this->timesSatisfied++;

            return true;
        }

        return false;
    }
}
