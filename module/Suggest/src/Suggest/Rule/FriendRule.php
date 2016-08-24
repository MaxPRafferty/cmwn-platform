<?php

namespace Suggest\Rule;

use Friend\NotFriendsException;
use Friend\Service\FriendServiceInterface;

/**
 * Class FriendRule
 * @package Suggest\Rule
 */
class FriendRule implements SuggestedRuleCompositeInterface
{
    /**
     * @var FriendServiceInterface
     */
    protected $friendService;

    /**
     * FriendRule constructor.
     * @param FriendServiceInterface $friendService
     */
    public function __construct($friendService)
    {
        $this->friendService = $friendService;
    }

    /**
     * @param \ArrayIterator $iterator
     * @param $currentUser
     * @return bool
     */
    public function checkIfAlreadyFriends($iterator, $currentUser)
    {
        try {
            $suggestion = $iterator->current();
            $this->friendService->fetchFriendStatusForUser($currentUser, $suggestion);

            $iterator->offsetUnset($suggestion->getUserId());
        } catch (NotFriendsException $nf) {
            //noop
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function apply($suggestionContainer, $currentUser)
    {
        $iterator = new \ArrayIterator($suggestionContainer);
        iterator_apply($iterator, [$this, "checkIfAlreadyFriends"], [$iterator, $currentUser]);
    }
}
