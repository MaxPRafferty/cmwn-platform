<?php

namespace Suggest\Service;

use Suggest\NotFoundException;
use User\UserInterface;
use Zend\Paginator\Adapter\DbSelect;

/**
 * Interface SuggestedFriendServiceInterface
 */
interface SuggestedServiceInterface
{
    /**
     * @param $user
     * @param $suggestion
     * @param null $prototype
     * @throws NotFoundException
     * @return UserInterface
     */
    public function fetchSuggestedFriendForUser($user, $suggestion, $prototype = null);

    /**
     * Fetches the suggested users for a user
     *
     * @param UserInterface $user
     * @param null $where
     * @param null $prototype
     * @return DbSelect
     */
    public function fetchSuggestedFriendsForUser($user, $where = null, $prototype = null);

    /**
     * adds friend suggestions for a user to the database
     *
     * @param UserInterface $user
     * @param UserInterface $suggestion
     */
    public function attachSuggestedFriendForUser($user, $suggestion);

    /**
     * @param UserInterface $user
     * @param UserInterface $suggestion
     * @return mixed
     */
    public function deleteSuggestionForUser($user, $suggestion);
}
