<?php

namespace Security;

use User\User;

/**
 * A User that is not logged in and just has guest privileges
 */
class GuestUser extends User implements SecurityUserInterface
{
    /**
     * @inheritDoc
     */
    public function comparePassword(string $password): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function compareCode(string $code): string
    {
        return static::CODE_INVALID;
    }

    /**
     * @inheritDoc
     */
    public function getCode(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function isSuper(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getRole(): string
    {
        return static::ROLE_GUEST;
    }

    /**
     * @inheritDoc
     */
    public function setRole(string $role)
    {
        // these are not the role we're looking for
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return 'guest';
    }
}
