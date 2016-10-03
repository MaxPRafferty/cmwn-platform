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
        parent::__construct('games');
        $this->setProps(['label' => 'Games']);
        $this->setRoute('api.rest.game', ['game_id' => $gameId]);
    }
}
