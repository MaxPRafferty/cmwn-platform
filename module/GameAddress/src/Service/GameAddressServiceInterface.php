<?php

namespace GameAddress\Service;

use Game\GameInterface;

interface GameAddressServiceInterface
{
    /**
     * @param GameInterface $game
     * @param array $fieldValues
     * @return mixed
     */
    public function attachGameToRegion(GameInterface $game, array $fieldValues = []);
}
