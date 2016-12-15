<?php

namespace Rule\Date;

use Application\Utils\Date\DateTimeFactory;
use Rule\Exception\InvalidArgumentException;
use Rule\RuleInterface;

/**
 * Handles the common checking of date rules
 */
abstract class AbstractDateRule implements RuleInterface
{
    const OPERATOR_EQUALS              = '=';
    const OPERATOR_LESS_THAN           = '<';
    const OPERATOR_LESS_THAN_EQUALS    = '=<';
    const OPERATOR_GREATER_THAN        = '>';
    const OPERATOR_GREATER_THAN_EQUALS = '>=';

    /**
     * @return \DateTime
     */
    protected function getCurrentDate(): \DateTime
    {
        return DateTimeFactory::factory('now');
    }

    /**
     * @param \DateTime $leftDate
     * @param $operator
     * @param \DateTime|null $rightDate
     *
     * @return bool
     */
    protected function compare(\DateTime $leftDate, $operator, \DateTime $rightDate = null): bool
    {
        // Normalize the date to correct for time zones
        $leftDate  = DateTimeFactory::factory($leftDate);
        $rightDate = $rightDate ?? $this->getCurrentDate();

        switch ($operator) {
            case static::OPERATOR_GREATER_THAN:
                return $leftDate->getTimestamp() < $rightDate->getTimestamp();

            case static::OPERATOR_GREATER_THAN_EQUALS:
                return $leftDate->getTimestamp() <= $rightDate->getTimestamp();

            case static::OPERATOR_EQUALS:
                return $leftDate->getTimestamp() == $rightDate->getTimestamp();

            case static::OPERATOR_LESS_THAN:
                return $leftDate->getTimestamp() > $rightDate->getTimestamp();

            case static::OPERATOR_LESS_THAN_EQUALS:
                return $leftDate->getTimestamp() >= $rightDate->getTimestamp();

            default:
                throw new InvalidArgumentException(sprintf('Invalid operator %s', $operator));
        }
    }

    /**
     * Returns the Modulus of 2 dates
     *
     * @todo handle crazy timezone offsets and then drink lots of alcohol when we want to
     * @param \DateTime $leftDate
     * @param \DateTime|null $rightDate
     *
     * @return int
     */
    protected function modDates(\DateTime $leftDate, \DateTime $rightDate = null): int
    {
        // Normalize the date to correct for time zones
        $leftDate  = DateTimeFactory::factory($leftDate);
        $rightDate = $rightDate ?? $this->getCurrentDate();

        return $leftDate->getTimestamp() % $rightDate->getTimestamp();
    }
}
