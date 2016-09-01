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
        foreach ($mergeContainer->getIterator() as $userId => $suggestion) {
            $this->offsetSet($userId, $suggestion);
        }
    }
}
