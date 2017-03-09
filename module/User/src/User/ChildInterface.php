<?php

namespace User;

/**
 * Allows a user to represented as a child
 *
 * Children cannot create their own user names.  To ensure this, children need to select a random username
 * This requires a child object to take in a UserName Object which holds the information for a created name
 *
 * @see UserName
 */
interface ChildInterface extends UserInterface
{
    /**
     * Tests if the name is generated or not
     *
     * @return bool
     */
    public function isNameGenerated(): bool;

    /**
     * Returns the generated name
     *
     * @return UserName
     */
    public function getGeneratedName(): UserName;

    /**
     * Sets the UserName on the child
     *
     *
     * @param UserName $username
     *
     * @return ChildInterface
     */
    public function setGeneratedName(UserName $username): ChildInterface;
}
