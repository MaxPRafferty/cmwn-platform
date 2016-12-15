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
}
