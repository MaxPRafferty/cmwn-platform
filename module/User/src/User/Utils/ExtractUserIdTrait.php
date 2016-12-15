<?php

namespace User\Utils;

use User\UserInterface;

/**
 * Trait ExtractUserIdTrait
 */
trait ExtractUserIdTrait
{
    /**
     * Helper function to extract a user id from either a user or just return the user id
     *
     * @param $user
     *
     * @return string
     */
    public function extractUserId($user)
    {
        return $user instanceof UserInterface ? $user->getUserId() : $user;
    }
}
