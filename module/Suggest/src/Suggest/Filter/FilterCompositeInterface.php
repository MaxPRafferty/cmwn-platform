<?php

namespace Suggest\Filter;

use Suggest\SuggestionCollection;
use User\UserInterface;

/**
 * Interface SuggestedRuleCompositeInterface
 *
 * A Filter returns a list of suggested friends from the database
 *
 * @package Suggest\Rule
 */
interface FilterCompositeInterface
{
    /**
     * @param SuggestionCollection $container
     * @param UserInterface $user
     *
     * @return mixed
     */
    public function getSuggestions(SuggestionCollection $container, UserInterface $user);
}
