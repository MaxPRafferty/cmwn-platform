<?php

namespace Rule\Date;

use Rule\Item\RuleItemInterface;
use Rule\RuleInterface;
use Rule\TimesSatisfiedTrait;

/**
 * Rule that states the current date must be after the specified date
 */
class DateAfterSpecification extends AbstractDateRule implements RuleInterface
{
    use TimesSatisfiedTrait;

    /**
     * @var \DateTime
     */
    protected $startDate;

    /**
     * DateStartSpecification constructor.
     *
     * @param \DateTime $startDate
     */
    public function __construct(\DateTime $startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @inheritDoc
     */
    public function isSatisfiedBy(RuleItemInterface $event): bool
    {
        if ($this->compare($this->startDate, static::OPERATOR_GREATER_THAN)) {
            $this->timesSatisfied++;
        }

        return $this->timesSatisfied > 0;
    }
}
