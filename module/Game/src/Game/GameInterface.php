<?php

namespace Game;

/**
 * Interface GameInterface
 *
 * @author Chuck "MANCHUCK" Reeves <chuck@manchuck.com>
 */
interface GameInterface
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
     * @return string
     */
    public function getGameId();

    /**
     * @param string $gameId
     * @return Game
     */
    public function setGameId($gameId);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     * @return Game
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     * @return Game
     */
    public function setDescription($description);
}
