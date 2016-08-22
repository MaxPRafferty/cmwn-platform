<?php

namespace Suggest;

use Friend\Friend;

/**
 * Class Suggestion
 * @package Suggest
 */
class Suggestion extends Friend implements SuggestionInterface
{
    /**
     * Replaces the user_id with friend_id
     *
     * @return string[]
     */
    public function getArrayCopy()
    {
        $array = parent::getArrayCopy();
        $array['suggest_id']    = $this->getUserId();
        $array['friend_status'] = $this->getFriendStatus();
        unset($array['friend_id']);
        return $array;
    }
}
