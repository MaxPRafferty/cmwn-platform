<?php

namespace Rule\Rule\Date;

use Rule\Item\RuleItemInterface;
use Rule\Rule\RuleInterface;
use Rule\Rule\TimesSatisfiedTrait;

/**
 * Rule that states the current date is before the specified date
 */
class DateBeforeRule extends AbstractDateRule implements RuleInterface
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
