<?php

namespace Suggest\Rule;

use Suggest\SuggestionCollection;
use User\UserInterface;

/**
 * Interface SuggestedRuleInterface
 */
interface RuleCompositeInterface
{
    /**
     * Applies a rule to every suggested friend in the suggestion container.
     *
     * @param SuggestionCollection $suggestionContainer A container of suggestions
     * @param UserInterface $currentUser                The user the engine is currently checking
     *
     * @return
     */
    public function apply(SuggestionCollection $suggestionContainer, UserInterface $currentUser);
}
