<?php

namespace Notice;

/**
 * Interface NotificationAwareInterface
 */
interface NotificationAwareInterface
{
    /**
     * Produces an email to be sent out to the specified email
     *
     * @return string
     */
    public function getEmail();

    /**
     * Weather this can produce a name to be included with the email
     *
     * @return bool
     */
    public function hasName();

    /**
     * Produces an name for the user
     *
     * @return string
     */
    public function getName();

    /**
     * Sets the email for notifications
     *
     * @param string $email
     */
    public function setEmail($email);
}
