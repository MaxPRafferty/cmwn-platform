<?php

namespace Suggest\Rule;

use Friend\NotFriendsException;
use Friend\Service\FriendServiceInterface;
use Suggest\SuggestionCollection;
use User\UserInterface;

/**
 * Class FriendRule
 *
 * Removes existing or pending friends from the suggestions
 *
 * @package Suggest\Rule
 */
class FriendRule implements RuleCompositeInterface
{
    /**
     * @var FriendServiceInterface
     */
    protected $friendService;

    /**
     * FriendRule constructor.
     *
     * @param FriendServiceInterface $friendService
     */
    public function __construct(FriendServiceInterface $friendService)
    {
        $this->friendService = $friendService;
    }

    /**
     * @inheritdoc
     */
    public function apply(SuggestionCollection $suggestionContainer, UserInterface $currentUser)
    {
        iterator_apply(
            $suggestionContainer,
            function (UserInterface $suggestion) use (&$suggestionContainer, $currentUser) {
                try {
                    $this->friendService->fetchFriendStatusForUser($currentUser, $suggestion);
                    $suggestionContainer->offsetUnset($suggestion->getUserId());
                } catch (NotFriendsException $nf) {
                    //noop
                }
            }
        );
    }
}
