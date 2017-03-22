<?php

namespace Game;

use Application\Utils\Date\DateCreatedInterface;

/**
 * An interface that defines a users saved game
 *
 * @SWG\Definition(
 *     definition="SaveGame",
 *     description="This allows game progress to be saved",
 *     required={"game_id","user_id","data","version"},
 *     @SWG\Property(
 *          type="string",
 *          format="uuid",
 *          property="game_id",
 *          description="The id of the game"
 *     ),
 *     @SWG\Property(
 *          type="string",
 *          format="uuid",
 *          property="user_id",
 *          description="The id of the user"
 *     ),
 *     @SWG\Property(
 *          type="string",
 *          property="version",
 *          description="The data version of the game (tells the game the last time the data was saved)"
 *     ),
 *     @SWG\Property(
 *          type="string",
 *          property="description",
 *          description="A Description for the game"
 *     )
 * )
 *
 * @FIXME set the right swagger spec for data
 */
interface SaveGameInterface extends DateCreatedInterface
{
    /**
     * Exchange internal values from provided array
     *
     * @param  array $array
     *
     * @return SaveGameInterface
     */
    public function exchangeArray(array $array): SaveGameInterface;

    /**
     * Return an array representation of the object
     *
     * @return array
     */
    public function getArrayCopy(): array;

    /**
     * Gets the Game Id
     *
     * @return string
     */
    public function getGameId(): string;

    /**
     * Sets the Game Id
     *
     * @param string $gameId
     *
     * @return SaveGameInterface
     */
    public function setGameId(string $gameId): SaveGameInterface;

    /**
     * Gets the UserId
     *
     * @return string
     */
    public function getUserId(): string;

    /**
     * Sets the User Id
     *
     * @param string $userId
     *
     * @return SaveGameInterface
     */
    public function setUserId(string $userId): SaveGameInterface;

    /**
     * Gets the Game Data
     *
     * @return array
     */
    public function getData(): array;

    /**
     * Saves the Game Data
     *
     * @param array|string $gameData
     *
     * @return SaveGameInterface
     */
    public function setData($gameData): SaveGameInterface;

    /**
     * Returns back the version that this game was saved at
     *
     * @return string
     */
    public function getVersion(): string;

    /**
     * Sets the version of the game data
     *
     * @param $version
     *
     * @return SaveGameInterface
     */
    public function setVersion(string $version): SaveGameInterface;
}
