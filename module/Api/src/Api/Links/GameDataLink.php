<?php

namespace Api\Links;

use ZF\Hal\Link\Link;

/**
 * Class GameDataLink
 *
 * Hal link for a game-data
 */
class GameDataLink extends Link
{
    /**
     * GameLink constructor.
     * @param $gameId
     */
    public function __construct($gameId = null)
    {
        parent::__construct('game-data');
        $this->setProps(['label' => 'Game Data']);
        $this->setRoute('api.rest.game-data', ['game_id' => $gameId]);
    }
}
