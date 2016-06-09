<?php

namespace Game;

use Zend\Filter\StaticFilter;

/**
 * Interface SaveGameInterface
 */
interface SaveGameInterface
{
    /**
     * Exchange internal values from provided array
     *
     * @param  array $array
     * @return void
     */
    public function exchangeArray(array $array);

    /**
     * Return an array representation of the object
     *
     * @return array
     */
    public function getArrayCopy();

    /**
     * Gets the Game Id
     *
     * @return string
     */
    public function getGameId();

    /**
     * Sets the Game Id
     *
     * @param string $gameId
     */
    public function setGameId($gameId);

    /**
     * Gets the UserId
     *
     * @return string
     */
    public function getUserId();

    /**
     * Sets the User Id
     *
     * @param string $userId
     */
    public function setUserId($userId);

    /**
     * Gets the Game Data
     *
     * @return array
     */
    public function getData();

    /**
     * Saves the Game Data
     *
     * @param array|string $gameData
     */
    public function setData($gameData);

    /**
     * @return \DateTime|null
     */
    public function getCreated();

    /**
     * @param \DateTime|string|null $created
     * @return $this
     */
    public function setCreated($created);

    /**
     * Returns back the version that this game was saved at
     *
     * @return string
     */
    public function getVersion();

    /**
     * Sets the version of the game data
     *
     * @param $version
     * @return $this
     */
    public function setVersion($version);
}
