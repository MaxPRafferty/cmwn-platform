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
     * @param $entity
     * @param string|null $deleted
     */
    public function __construct($entity, $deleted = null)
    {
        $label = 'games';
        $query = null;
        $propsLabel = 'Games';

        if ($deleted === 'true') {
            $label .= '_deleted';
            $query = ['deleted' => $deleted];
            $propsLabel = 'Deleted Games';
        }

        parent::__construct($label);
        $this->setProps(['label' => $propsLabel]);
        $this->setRoute('api.rest.game', [], ['query' => $query, 'reuse_matched_params' => false]);
    }
}
