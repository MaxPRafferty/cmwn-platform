<?php

namespace Suggest\Rule;

use Suggest\SuggestionContainer;
use User\UserInterface;

/**
 * Class MeRule
 *
 * Prevents suggesting their self as a friend
 * @package Suggest\Rule
 */
class MeRule implements SuggestedRuleCompositeInterface
{
    /**
     * @inheritdoc
     */
    public function apply(SuggestionContainer $suggestionContainer, UserInterface $currentUser)
    {
        if ($suggestionContainer->offsetExists($currentUser->getUserId())) {
            $suggestionContainer->offsetUnset($currentUser->getUserId());
        }
    }
}
