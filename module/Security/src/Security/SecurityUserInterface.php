<?php

namespace Security;

/**
 * A Security user is a user with sensitive information
 */
interface SecurityUserInterface
{
    const CODE_EXPIRED = 'Expired';
    const CODE_INVALID = 'Invalid';
    const CODE_VALID   = 'Valid';

    const ROLE_GUEST     = 'guest';
    const ROLE_LOGGED_IN = 'logged_in';

    /**
     * Verifies the password
     *
     * @param $password
     *
     * @return bool
     */
    public function comparePassword(string $password): bool;

    /**
     * Compare string to a code
     *
     * @param $code
     *
     * @return string
     */
    public function compareCode(string $code): string;

    /**
     * Gets the temp code for the user
     *
     * @return string
     */
    public function getCode(): string;

    /**
     * Tests if the user is a super admin or not
     *
     * @return bool
     */
    public function isSuper(): bool;

    /**
     * Gets the role the user has
     *
     * @return string
     */
    public function getRole(): string;

    /**
     * Manually set a role the user has
     *
     * @param string $role
     */
    public function setRole(string $role);
}
