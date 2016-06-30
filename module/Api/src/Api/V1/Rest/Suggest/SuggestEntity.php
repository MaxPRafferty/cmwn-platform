<?php

namespace Api\V1\Rest\Suggest;

use Api\V1\Rest\Friend\FriendEntity;

/**
 * Class SuggestEntity
 */
class SuggestEntity extends FriendEntity
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
