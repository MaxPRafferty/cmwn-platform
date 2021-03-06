<?php

namespace Application\Utils\Date;

/**
 * A Static factory that will transform strings or ints to a datetime
 *
 * All timezones will be set to UTC
 */
abstract class DateTimeFactory
{
    /**
     * Converts a string to a \DateTime
     *
     * Sets the timezone to be UTC
     *
     * @param  \DateTime|string|int|null $date
     * @return \DateTime|null
     */
    public static function factory($date)
    {
        if ($date === null) {
            return $date;
        }

        // Timestamp
        if (is_int($date)) {
            $date = date(\DateTime::ISO8601, $date);
        }

        if (!$date instanceof \DateTime) {
            $date = new \DateTime($date);
        }

        $date->setTimezone(new \DateTimeZone('UTC'));
        return $date;
    }
}
