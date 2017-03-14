<?php

namespace User;

use User\Service\StaticNameService;

/**
 * A Child User
 */
class Child extends User implements ChildInterface
{
    /**
     * @var UserName
     */
    protected $generatedName;

    /**
     * @inheritdoc
     */
    public function getType(): string
    {
        return static::TYPE_CHILD;
    }

    /**
     * Generates a random user name for the child
     *
     * @return string|null
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
     * @inheritdoc
     */
    public function setGeneratedName(UserName $username): ChildInterface
    {
        $this->generatedName = $username;
        $this->userName      = $username->userName;

        return $this;
    }

    /**
     * @inheritdoc
     */

    public function setUserName(string $userName): UserInterface
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
    public function isNameGenerated(): bool
    {
        return $this->generatedName !== null;
    }

    /**
     * @inheritdoc
     */
    public function getGeneratedName(): UserName
    {
        return $this->generatedName;
    }

    /**
     * Overrides the email and sets it to the <username>@changemyworldnow.com
     */
    public function getEmail():string
    {
        $this->setEmail($this->getUserName() . '@changemyworldnow.com');
        return parent::getEmail();
    }
}
