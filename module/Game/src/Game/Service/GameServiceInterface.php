<?php

namespace Game\Service;

use Game\GameInterface;
use Zend\Paginator\Adapter\AdapterInterface;

/**
 * Defines an interface for a service that transfer games to the database
 */
interface GameServiceInterface
{
    /**
     * Fetches all games
     *
     * @param null|array $where filter out based on fields matching
     * @param GameInterface|null $prototype they type of game you wish returned
     * @param bool $deleted wheather to include deleted games
     *
     * @return AdapterInterface
     */
    public function fetchAll($where = null, GameInterface $prototype = null, bool $deleted = false): AdapterInterface;

    /**
     * Fetches one game by title
     *
     * @param string $gameId the id of the game to fetch
     * @param GameInterface|null $prototype hydrated this type of game
     *
     * @return GameInterface
     */
    public function fetchGame(string $gameId, GameInterface $prototype = null): GameInterface;

    /**
     * Saves a game to the database
     *
     * @param GameInterface $game the game to save
     *
     * @return bool
     */
    public function saveGame(GameInterface $game): bool;

    /**
     * Creates a new game in the database
     *
     * @param GameInterface $game the game to create
     *
     * @return bool
     */
    public function createGame(GameInterface $game): bool;

    /**
     * Deletes a game in the database
     *
     * @param GameInterface $game the game to delete
     * @param bool $soft weather to soft or hard delete a game
     *
     * @return bool
     */
    public function deleteGame(GameInterface $game, bool $soft = true): bool;
}
