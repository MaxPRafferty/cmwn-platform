<?php

namespace Suggest\Rule;

use Suggest\SuggestionContainer;
use User\UserInterface;

/**
 * Interface SuggestedRuleInterface
 */
interface SuggestedRuleCompositeInterface
{
    /**
     * @param SuggestionContainer $suggestionContainer
     * @param UserInterface $currentUser
     */
    public function apply($suggestionContainer, $currentUser);
}
