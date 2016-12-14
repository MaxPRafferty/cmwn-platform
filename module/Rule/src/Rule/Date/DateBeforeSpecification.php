<?php

namespace Rule\Date;

use Rule\RuleItemInterface;
use Rule\RuleInterface;
use Rule\TimesSatisfiedTrait;

/**
 * Rule that states the current date is before the specified date
 */
class DateBeforeSpecification extends AbstractDateRule implements RuleInterface
{
    use TimesSatisfiedTrait;

    /**
     * @var \DateTime
     */
    protected $endDate;

    /**
     * DateStartSpecification constructor.
     *
     * @param \DateTime $endDate
     */
    public function __construct(\DateTime $endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * @inheritDoc
     */
    public function isSatisfiedBy(RuleItemInterface $event): bool
    {
        if ($this->compare($this->endDate, static::OPERATOR_LESS_THAN_EQUALS)) {
            $this->timesSatisfied++;
        }

        return $this->timesSatisfied > 0;
    }
}
