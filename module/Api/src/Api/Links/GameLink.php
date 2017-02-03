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
     * @param string|null $deleted
     */
    public function __construct($deleted = null)
    {
        $query = $deleted === 'true' ? ['deleted' => $deleted] : null;
        parent::__construct('games');
        $this->setProps(['label' => 'Games']);
        $this->setRoute('api.rest.game', [], ['query' => $query, 'reuse_matched_params' => false]);
    }
}
