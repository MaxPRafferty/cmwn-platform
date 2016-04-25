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
     */
    public function __construct()
    {
        parent::__construct('games');
        $this->setRoute('api.rest.game');
    }
}
