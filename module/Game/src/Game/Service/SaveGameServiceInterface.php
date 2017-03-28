<?php

namespace Game\Service;

use Application\Exception\NotFoundException;
use Game\GameInterface;
use Game\SaveGame;
use Game\SaveGameInterface;
use User\UserInterface;
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\Paginator\Adapter\AdapterInterface;

/**
 * Defines an interface to save game data for a user
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
    public function saveGame(SaveGameInterface $gameData): bool;

    /**
     * Deletes a save for a user
     *
     * @param UserInterface $user the user or the user id
     * @param GameInterface $game the game or the game id
     *
     * @return bool
     */
    public function deleteSaveForUser(UserInterface $user, GameInterface $game): bool;

    /**
     * Fetches a save for a user
     *
     * @param UserInterface $user               the user or the user id
     * @param GameInterface $game               the game or the game id
     * @param SaveGameInterface|null $prototype the type of save game to hydrate
     * @param null|array|PredicateSet $where    passes options for the select
     *
     * @return SaveGameInterface
     * @throws NotFoundException
     */
    public function fetchSaveGameForUser(
        UserInterface $user,
        GameInterface $game,
        $where = null,
        SaveGameInterface $prototype = null
    ): SaveGameInterface;

    /**
     * Fetch all saves for user
     *
     * @param UserInterface $user               the user or the user id
     * @param SaveGameInterface|null $prototype the type of save game to hydrate
     * @param null|array|PredicateSet $where    passes options for the select
     *
     * @return AdapterInterface
     */
    public function fetchAllSaveGamesForUser(
        UserInterface $user,
        $where = null,
        SaveGameInterface $prototype = null
    ): AdapterInterface;

    /**
     * Fetch all saves for game
     *
     * @param null|array|PredicateSet $where    passes options for the select
     * @param SaveGameInterface|null $prototype the type of save game to hydrate
     *
     * @return AdapterInterface
     */
    public function fetchAllSaveGameData($where = null, SaveGameInterface $prototype = null): AdapterInterface;
}
