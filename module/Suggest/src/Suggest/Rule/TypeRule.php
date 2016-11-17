<?php

namespace Suggest\Rule;

use Suggest\SuggestionContainer;
use User\UserInterface;

/**
 * Class StaticFilterSuggestions
 *
 * Matches the types of users together
 * @package Suggest\Engine
 */
class TypeRule implements SuggestedRuleCompositeInterface
{
    /**
     * @inheritdoc
     */
    public function apply(SuggestionContainer $suggestionContainer, UserInterface $currentUser)
    {
        /** @var UserInterface $suggestion */
        foreach ($suggestionContainer as $suggestion) {
            // Removed on a previous iteration
            if (!$suggestionContainer->offsetExists($suggestion->getUserId())) {
                continue;
            }

            if ($suggestion->getType() !== $currentUser->getType()) {
                $suggestionContainer->offsetUnset($suggestion->getUserId());
            }
        }
    }
}
