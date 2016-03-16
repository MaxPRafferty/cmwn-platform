<?php

namespace User;

/**
 * Class UserAwareInterface
 */
interface UserAwareInterface
{
    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user);

    /**
     * @return UserInterface
     */
    public function getUser();
}
