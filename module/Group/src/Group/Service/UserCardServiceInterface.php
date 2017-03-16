<?php

namespace Group\Service;

use Group\GroupInterface;

interface UserCardServiceInterface
{
    public function generateUserCards(GroupInterface $group);
}
