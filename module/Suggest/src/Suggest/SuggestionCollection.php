<?php

namespace Suggest;

use User\UserInterface;

/**
 * Class SuggestionContainer
 */
class SuggestionCollection extends \ArrayObject
{
    /**
     * Ensures that only users are passed as suggestions
     *
     * @param mixed $suggestionId
     * @param mixed $suggestion
     */
    public function offsetSet($suggestionId, $suggestion)
    {
        if (!$suggestion instanceof UserInterface) {
            throw new InvalidSuggestionException();
        }

        parent::offsetSet($suggestion->getUserId(), $suggestion);
    }

    /**
     * Merges two collections
     *
     * @param SuggestionCollection $mergeContainer
     */
    public function merge(SuggestionCollection $mergeContainer)
    {
        iterator_apply($mergeContainer, function (UserInterface $suggestion) {
            if (!$this->offsetExists($suggestion->getUserId())) {
                $this->offsetSet($suggestion->getUserId(), $suggestion);
            }
        });
    }
}
