<?php

namespace Notice;

/**
 * Trait NotificationAwareTrait
 *
 * ${CARET}
 */
trait NotificationAwareTrait
{
    /**
     * @var string
     */
    protected $email;

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return bool
     */
    public function hasName()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return '';
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }
}
