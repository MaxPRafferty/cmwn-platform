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
     *
     * @return
     */
    public function apply(SuggestionContainer $suggestionContainer, UserInterface $currentUser);
}
