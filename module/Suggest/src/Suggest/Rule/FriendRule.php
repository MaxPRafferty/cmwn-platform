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
     * @inheritdoc
     */
    public function apply($suggestionContainer, $currentUser)
    {
        $iterator = $suggestionContainer->getIterator();

        foreach ($iterator as $key => $suggestion) {
            try {
                $this->friendService->fetchFriendStatusForUser($currentUser, $suggestion);
                $iterator->offsetUnset($key);
            } catch (NotFriendsException $nf) {
                //noop
            }
        }
    }
}
