<?php

namespace Suggest\Rule;

/**
 * Class MeRule
 * @package Suggest\Rule
 */
class MeRule implements SuggestedRuleCompositeInterface
{
    /**
     * @inheritdoc
     */
    public function apply($suggestionContainer, $currentUser)
    {
        $iterator = $suggestionContainer->getIterator();
        foreach ($suggestionContainer as $key => $suggestion) {
            if ($currentUser->getUserId() === $suggestion->getUserId()) {
                $iterator->offsetUnset($key);
            }
        }
    }
}
