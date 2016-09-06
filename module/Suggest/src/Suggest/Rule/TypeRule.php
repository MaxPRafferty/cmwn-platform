<?php

namespace Suggest\Rule;

/**
 * Class StaticFilterSuggestions
 * @package Suggest\Engine
 */
class TypeRule implements SuggestedRuleCompositeInterface
{
    /**
     * @inheritdoc
     */
    public function apply($suggestionContainer, $currentUser)
    {
        $iterator = $suggestionContainer->getIterator();

        foreach ($iterator as $key => $suggestion) {
            if ($currentUser->getType()!= $suggestion->getType()) {
                $iterator->offsetUnset($key);
            }
        }
    }
}
