<?php

namespace Group;

/**
 * Interface GroupAwareInterface
 */
interface GroupAwareInterface
{
    /**
     * Sets the group to this object
     *
     * @param GroupInterface|string $group
     */
    public function setGroup($group);
}
