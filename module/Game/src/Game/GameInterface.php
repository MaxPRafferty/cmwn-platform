<?php

namespace Game;

use Application\Utils\Date\SoftDeleteInterface;
use Application\Utils\Date\StandardDateInterface;
use Application\Utils\Meta\MetaDataInterface;

/**
 * Game
 *
 * A Game is a specification which represents each game in the system. It contains all the information about the
 * game like when is it creation, updation and deletion times, if it is available to be played, and other meta info.
 *
 * @SWG\Definition(
 *     definition="Game",
 *     description="A Game represents every game in the system and details about it",
 *     required={"game_id","title","description","coming_soon"},
 *     x={
 *          "search-doc-id":"game_id",
 *          "search-doc-type":"game"
 *     },
 *     allOf={
 *          @SWG\Schema(ref="#/definitions/DateCreated"),
 *          @SWG\Schema(ref="#/definitions/DateUpdated"),
 *          @SWG\Schema(ref="#/definitions/DateDeleted"),
 *     },
 *     @SWG\Property(
 *          type="string",
 *          format="uuid",
 *          property="game_id",
 *          description="The id of the game (usually the slug of game title)"
 *     ),
 *     @SWG\Property(
 *          type="string",
 *          property="title",
 *          description="Title of the game"
 *     ),
 *     @SWG\Property(
 *          type="string",
 *          property="description",
 *          description="A Description for the game"
 *     ),
 *     @SWG\Property(
 *          type="boolean",
 *          property="coming_soon",
 *          readOnly=true,
 *          description="Whether the game is coming_soon"
 *     ),
 *     @SWG\Property(
 *          type="boolean",
 *          property="global",
 *          readOnly=true,
 *          description="Whether the game is global"
 *     ),
 * )
 */
interface GameInterface extends
    SoftDeleteInterface,
    StandardDateInterface,
    MetaDataInterface
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

    /**
     * Add a value to meta data
     *
     * @param $key
     * @param $value
     */
    public function addToMeta($key, $value);
}
