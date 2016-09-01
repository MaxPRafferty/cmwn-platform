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
        if ($iterator->offsetExists($currentUser->getUserId())) {
            $iterator->offsetUnset($currentUser->getUserId());
        }
    }
}
