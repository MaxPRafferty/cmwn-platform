<?php

namespace Security\Service;

use User\UserInterface;

/**
 * Interface SecurityGroupServiceInterface
 */
interface SecurityGroupServiceInterface
{

    /**
     * @param UserInterface $activeUser
     * @param UserInterface $requestedUser
     * @return string
     */
    public function fetchRelationshipRole(UserInterface $activeUser, UserInterface $requestedUser);
}
