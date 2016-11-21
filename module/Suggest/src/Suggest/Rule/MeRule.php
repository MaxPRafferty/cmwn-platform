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
    public function apply(SuggestionCollection $suggestionCollection, UserInterface $currentUser)
    {
        if ($suggestionCollection->offsetExists($currentUser->getUserId())) {
            $suggestionCollection->offsetUnset($currentUser->getUserId());
        }
    }
}
