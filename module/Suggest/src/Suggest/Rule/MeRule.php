<?php

namespace Suggest\Rule;

use Suggest\SuggestionCollection;
use User\UserInterface;

/**
 * Class Me Rule
 *
 * Prevents suggesting their self as a friend
 * @package Suggest\Rule
 */
class MeRule implements RuleCompositeInterface
{
    /**
     * @inheritdoc
     */
    public function apply(SuggestionCollection $suggestionContainer, UserInterface $currentUser)
    {
        if ($suggestionContainer->offsetExists($currentUser->getUserId())) {
            $suggestionContainer->offsetUnset($currentUser->getUserId());
        }
    }
}
