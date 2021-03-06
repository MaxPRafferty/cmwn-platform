<?php

namespace Suggest;

use Friend\Friend;
use User\UserInterface;

/**
 * Class Suggestion
 *
 * @package Suggest
 */
class Suggestion extends Friend implements SuggestionInterface
{
    /**
     * @inheritdoc
     */
    public function getArrayCopy(): array
    {
        $array                  = parent::getArrayCopy();
        $array['suggest_id']    = $this->getUserId();
        $array['friend_status'] = $this->getFriendStatus();
        unset($array['friend_id']);

        return $array;
    }

    /**
     * @inheritdoc
     */
    public function exchangeArray(array $array): UserInterface
    {
        $array['suggest_id']    = isset($array['suggest_id']) ? $array['suggest_id'] : null;
        $array['friend_status'] = isset($array['friend_status']) ? $array['friend_status'] : null;

        parent::exchangeArray($array);
        return $this;
    }
}
