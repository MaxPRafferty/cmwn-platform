<?php

namespace Game\Service;

use Application\Exception\NotFoundException;
use Game\SaveGame;
use Game\SaveGameInterface;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Paginator\Adapter\DbSelect;

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
     * //TODO Change the signature to have the parameter order be $where then $prototype.
     * @return SaveGame|SaveGameInterface
     * @throws NotFoundException
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function fetchSaveGameForUser($user, $game, $prototype = null, $where = null);

    /**
     * Fetch all saves for user
     *
     * @param $user
     * @param null $where
     * @param null $prototype
     * @return mixed
     */
    public function fetchAllSaveGamesForUser($user, $where = null, $prototype = null);

    /**
     * Fetch all saves for game
     * @param null $where
     * @param null $prototype
     * @return HydratingResultSet|DbSelect
     */
    public function fetchAllSaveGameData($where = null, $prototype = null);
}
