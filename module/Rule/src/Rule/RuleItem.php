<?php

namespace Rule;

use User\UserInterface;

/**
 * Class RuleItem
 */
class RuleItem implements RuleItemInterface
{
    /**
     * @var UserInterface
     */
    protected $activeUser;

    /**
     * @var array
     */
    protected $data;

    /**
     * @inheritDoc
     */
    public function __construct(UserInterface $activeUser = null, array $data = [])
    {
        $this->exchangeArray($data);
        if (null !== $activeUser) {
            $this->setActiveUser($activeUser);
        }
    }

    /**
     * @inheritDoc
     */
    public function setActiveUser(UserInterface $user)
    {
        $this->activeUser = $user;
    }

    /**
     * @inheritDoc
     */
    public function getActiveUser(): UserInterface
    {
        return clone $this->activeUser;
    }

    /**
     * @inheritDoc
     */
    public function getArrayCopy(): array
    {
        return $this->data;
    }

    /**
     * @inheritDoc
     */
    public function exchangeArray(array $data)
    {
        $this->data = $data;
    }
}
