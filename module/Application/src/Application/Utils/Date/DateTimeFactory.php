<?php

namespace Application\Utils\Date;

class DateTimeFactory
{
    /**
     * Converts a string to a \DateTime
     *
     * Sets the timezone to be UTC
     *
     * @param  \DateTime|string|null $date
     * @return \DateTime|null
     */
    public static function factory($date)
    {
        if ($date === null) {
            return $date;
        }

        if (!$date instanceof \DateTime) {
            $date = new \DateTime($date);
        }

        $date->setTimezone(new \DateTimeZone('UTC'));
        return $date;
    }
}
