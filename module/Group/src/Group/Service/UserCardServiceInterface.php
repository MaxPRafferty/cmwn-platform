<?php

namespace Group\Service;

use Group\GroupInterface;

/**
 * Specification for user card service
 */
interface UserCardServiceInterface
{
    /**
     * @param GroupInterface $group
     * @return mixed
     */
    public function generateUserCards(GroupInterface $group);
}
