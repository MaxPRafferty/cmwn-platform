<?php

namespace Friend\Service;

/**
 * Interface SuggestedFriendServiceInterface
 */
interface SuggestedFriendServiceInterface
{

    /**
     * Fetches the suggested users for a user
     *
     * @param $user
     * @param null $where
     * @param null $prototype
     * @return \Zend\Paginator\Adapter\DbSelect
     */
    public function fetchSuggestedFriends($user, $where = null, $prototype = null);
}
