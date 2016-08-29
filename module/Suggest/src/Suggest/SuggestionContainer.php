<?php

namespace Suggest;

/**
 * Class SuggestionContainer
 */
class SuggestionContainer extends \ArrayObject
{
    /**
     * merge new container in to this container without duplicates
     * @param SuggestionContainer $mergeContainer
     */
    public function merge(SuggestionContainer $mergeContainer)
    {
        $mergeSuggestions = $mergeContainer->getArrayCopy();
        $thisSuggestions = $this->getArrayCopy();
        foreach ($mergeSuggestions as $userId => $suggestion) {
            if (!array_key_exists($userId, $thisSuggestions)) {
                $thisSuggestions[$userId] = $suggestion;
            }
        }
        $this->exchangeArray($thisSuggestions);
    }
}
