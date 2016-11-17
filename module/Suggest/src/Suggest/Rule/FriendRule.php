<?php

namespace Suggest\Rule;

use Friend\NotFriendsException;
use Friend\Service\FriendServiceInterface;
use Suggest\SuggestionContainer;
use User\UserInterface;

/**
 * Class FriendRule
 *
 * Removes existing or pending friends from the suggestions
 *
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
    public function apply(SuggestionContainer $suggestionContainer, UserInterface $currentUser)
    {
        foreach ($suggestionContainer as $suggestion) {
            try {
                $this->friendService->fetchFriendStatusForUser($currentUser, $suggestion);
                $suggestionContainer->offsetUnset($suggestion->getUserId());
            } catch (NotFriendsException $nf) {
                //noop
            }
        }
    }
}
