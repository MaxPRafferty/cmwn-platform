<?php

namespace Api\Links;

use ZF\Hal\Link\Link;

/**
 * Class GameLink
 *
 * Hal link for a game
 */
class GameLink extends Link
{
    /**
     * GameLink constructor.
     * @param $gameId
     */
    public function __construct($gameId = null)
    {
        parent::__construct('game');
        $this->setProps(['label' => 'Game']);
        $this->setRoute('api.rest.game', ['game_id' => $gameId]);
    }
}
