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
    public function apply(SuggestionCollection $suggestionCollection, UserInterface $currentUser)
    {
        $suggestIterator = $suggestionCollection->getIterator();
        $suggestIterator->rewind();
        do {
            /** @var UserInterface $suggested */
            $suggested = $suggestIterator->current();
            $suggestIterator->next();
            if ($suggested === null) {
                break;
            }

            try {
                $this->friendService->fetchFriendStatusForUser($currentUser, $suggested);
                $suggestionCollection->offsetUnset($suggested->getUserId());
            } catch (NotFriendsException $notFriends) {
                // noop
            }
        } while (true);
    }
}
