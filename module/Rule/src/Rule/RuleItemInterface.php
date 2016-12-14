<?php

namespace Rule;

use User\UserInterface;

/**
 * A Rule item is an expanded event that is use to satisfy rules
 */
interface RuleItemInterface
{
    /**
     * Sets the current user that is triggering the event
     *
     * @param UserInterface $user
     */
    public function setActiveUser(UserInterface $user): void;

    /**
     * Gets the Active user that triggered the event
     *
     * @return UserInterface
     */
    public function getActiveUser(): UserInterface;

    /**
     * Gets all the data for the Item
     *
     * @return array
     */
    public function getArrayCopy(): array;

    /**
     * Sets the data for the object
     *
     * @param array $data
     */
    public function exchangeArray(array $data): void;
}
