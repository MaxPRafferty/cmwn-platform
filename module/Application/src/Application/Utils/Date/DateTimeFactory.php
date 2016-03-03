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

        try {
            if (!$date instanceof \DateTime) {
                $date = new \DateTime($date);
            }
            
        // Handle time stamps 
        // Remind me to tell Derick that he now owes me a beer ;)
        } catch (\Exception $dateException) {
            if (!$date instanceof \DateTime) {
                $date = new \DateTime(date('Y-m-d H:i:s', $date));
            }
        }

        $date->setTimezone(new \DateTimeZone('UTC'));
        return $date;
    }
}
