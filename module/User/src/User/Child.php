<?php

namespace User;

use User\Service\StaticNameService;

/**
 * Class Child
 * @package User
 */
class Child extends User implements ChildInterface
{
    /**
     * @var UserName|null
     */
    protected $generatedName;

    /**
     * @return string
     */
    public function getType()
    {
        return static::TYPE_CHILD;
    }

    /**
     * Generates a random user name for the child
     *
     * @return UserName
     */
    public function getUserName()
    {
        if (parent::getUserName() === null) {
            $generatedName = StaticNameService::generateRandomName();
            $this->setUserName($generatedName->userName);
            $this->generatedName = $generatedName;
        }

        return $this->isNameGenerated()
            ? $this->generatedName->userName
            : parent::getUserName();
    }

    /**
     * @param string $userName
     * @return $this
     */
    public function setUserName($userName)
    {
        if ($this->userName === null) {
            parent::setUserName($userName);
            $this->generatedName = null;
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isNameGenerated()
    {
        return $this->generatedName !== null;
    }

    /**
     * @return null|UserName
     */
    public function getGeneratedName()
    {
        return $this->generatedName;
    }

    /**
     * Will default the email to be username@changemyworldnow.com if empty
     * @return string
     */
    public function getEmail()
    {
        if (empty($this->email)) {
            $this->setEmail($this->getUserName() . '@changemyworldnow.com');
        }

        return $this->email;
    }
}
