<?php

namespace Api\Links;

use User\UserInterface;
use ZF\Hal\Link\Link;

/**
 * Class SaveGameLink
 */
class SaveGameLink extends Link
{
    /**
     * SaveGameLink constructor.
     *
     * @param string | UserInterface $user
     */
    public function __construct($user)
    {
        $userId = $user instanceof UserInterface ? $user->getUserId() : $user;
        parent::__construct('save_game');
        $this->setRoute('api.rest.save-game', ['reuse_matched_params' => false]);
        $this->setRouteParams(['game_id' => '{game_id}', 'user_id' => $userId]);
        $this->setProps(['Label' => 'Save Game', 'templated' => true]);
    }
}
