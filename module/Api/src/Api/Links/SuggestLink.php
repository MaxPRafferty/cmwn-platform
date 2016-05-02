<?php

namespace Api\Links;

use ZF\Hal\Link\Link;

/**
 * Class SuggestLink
 */
class SuggestLink extends Link
{
    /**
     * FriendLink constructor.
     * @param string $userId
     */
    public function __construct($userId)
    {
        parent::__construct('suggested_friends');
        $this->setRoute('api.rest.suggest', ['user_id' => $userId], ['reuse_matched_params' => false]);
    }
}
