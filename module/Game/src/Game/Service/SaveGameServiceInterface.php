<?php

namespace Game\Service;

use Application\Exception\NotFoundException;
use Game\SaveGame;
use Game\SaveGameInterface;

/**
 * Interface SaveGameServiceInterface
 */
interface SaveGameServiceInterface
{
    /**
     * Saves a game
     *
     * @param SaveGameInterface $gameData
     *
     * @return bool
     */
    public function saveGame(SaveGameInterface $gameData);

    /**
     * Deletes a save for a user
     *
     * @param $user
     * @param $game
     *
     * @return bool
     */
    public function deleteSaveForUser($user, $game);

    /**
     * Fetches a save for a user
     *
     * @param $user
     * @param $game
     * @param null $prototype
     * @param null $where
     *
     * @return SaveGame|SaveGameInterface
     * @throws NotFoundException
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function fetchSaveGameForUser($user, $game, $prototype = null, $where = null);
}
