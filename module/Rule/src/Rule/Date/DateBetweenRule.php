<?php

namespace Rule\Date;

use Rule\Basic\AndRule;

/**
 * Rule that states the date must be between 2 specified
 */
class DateBetweenRule extends AndRule
{
    /**
     * @inheritDoc
     */
    public function __construct(\DateTime $dateStart, \DateTime $dateEnd)
    {
        parent::__construct(
            new DateAfterRule($dateStart),
            new DateBeforeRule($dateEnd)
        );
    }
}
