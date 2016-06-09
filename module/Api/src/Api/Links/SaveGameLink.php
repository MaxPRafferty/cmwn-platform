<?php

namespace Api\Links;

use ZF\Hal\Link\Link;

/**
 * Class SaveGameLink
 */
class SaveGameLink extends Link
{
    /**
     * SaveGameLink constructor.
     *
     * @param string $userId
     */
    public function __construct($userId)
    {
        parent::__construct('save_game');
        $this->setRoute('api.rest.save-game', ['reuse_matched_params' => false]);
        $this->setRouteParams(['game_id' => '{game_id}', 'user_id' => $userId]);
        $this->setProps(['Label' => 'Save Game', 'templated' => true]);
    }
}
