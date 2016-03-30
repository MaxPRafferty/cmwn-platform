<?php


namespace Api\Links;


use ZF\Hal\Link\Link;

class GameLink extends Link
{
    public function __construct()
    {
        parent::__construct('games');
        $this->setRoute('api.rest.game');
    }
}
