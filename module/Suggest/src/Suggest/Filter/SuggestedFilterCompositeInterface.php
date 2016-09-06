<?php

namespace Suggest\Filter;

use Suggest\SuggestionContainer;
use User\UserInterface;

/**
 * Interface SuggestedRuleCompositeInterface
 * @package Suggest\Rule
 */
interface SuggestedFilterCompositeInterface
{
    /**
     * @param UserInterface $user
     * @return SuggestionContainer
     */
    public function getSuggestions($user);
}
