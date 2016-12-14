<?php

namespace Rule\Date;

use Rule\Basic\AndSpecification;

/**
 * Rule that states the date must be between 2 specified
 */
class DateBetweenSpecification extends AndSpecification
{
    /**
     * @inheritDoc
     */
    public function __construct(\DateTime $dateStart, \DateTime $dateEnd)
    {
        parent::__construct(
            new DateAfterSpecification($dateStart),
            new DateBeforeSpecification($dateEnd)
        );
    }
}
