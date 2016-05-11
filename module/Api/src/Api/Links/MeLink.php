<?php

namespace Api\Links;

use User\UserInterface;
use ZF\Hal\Link\Link;

/**
 * Class MeLink
 *
 * Hal Link for Me
 */
class MeLink extends Link
{
    /**
     * MeLink constructor.
     * @param string $userId
     */
    public function __construct($userId)
    {
        $userId = $userId instanceof UserInterface ? $userId->getUserId() : $userId;
        parent::__construct('me');
        $this->setProps(['label' => 'Me']);
        $this->setRoute('api.rest.user', ['user_id' => $userId]);
    }
}
