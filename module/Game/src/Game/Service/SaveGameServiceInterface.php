<?php

namespace Game\Service;

use Application\Exception\NotFoundException;
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
     * @param UserInterface|string $user the user or the user id
     * @param UserInterface|string $game the game or the game id
     *
     * @todo Update to take in a game and a user
     *
     * @return bool
     */
    public function deleteSaveForUser($user, $game): bool;

    /**
     * Fetches a save for a user
     *
     * @param UserInterface|string $user        the user or the user id
     * @param UserInterface|string $game        the game or the game id
     * @param SaveGameInterface|null $prototype the type of save game to hydrate
     * @param null|array|PredicateSet $where    passes options for the select
     *
     * @todo Change the signature to have the parameter order be $where then $prototype.
     * @todo change user and game to be a user and game
     *
     * @return SaveGame|SaveGameInterface
     * @throws NotFoundException
     */
    public function fetchSaveGameForUser(
        $user,
        $game,
        SaveGameInterface $prototype = null,
        $where = null
    ): SaveGameInterface;

    /**
     * Fetch all saves for user
     *
     * @param UserInterface|string $user        the user or the user id
     * @param SaveGameInterface|null $prototype the type of save game to hydrate
     * @param null|array|PredicateSet $where    passes options for the select
     *
     * @return AdapterInterface
     */
    public function fetchAllSaveGamesForUser(
        $user,
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
