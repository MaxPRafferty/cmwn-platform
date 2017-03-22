<?php

namespace Game;

use Application\Utils\Date\SoftDeleteInterface;
use Application\Utils\Date\StandardDateInterface;
use Application\Utils\Flags\FlagInterface;
use Application\Utils\Meta\MetaDataInterface;
use Application\Utils\Sort\SortableInterface;
use Application\Utils\Uri\UriCollectionAwareInterface;

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
 *          description="The id of the game"
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
 *          type="string",
 *          property="game_url",
 *          format='url',
 *          description="The Url to play the game"
 *     ),
 *     @SWG\Property(
 *          type="string",
 *          property="thumb_url",
 *          format='url',
 *          description="The Url for the game thumbnail"
 *     ),
 *     @SWG\Property(
 *          type="string",
 *          property="banner_url",
 *          format='url',
 *          description="The Url for the game banner image"
 *     ),
 *     @SWG\Property(
 *          type="boolean",
 *          property="coming_soon",
 *          description="Whether the game should be displayed as coming soon"
 *     ),
 *     @SWG\Property(
 *          type="boolean",
 *          property="global",
 *          description="Whether the game is a global game"
 *     ),
 *     @SWG\Property(
 *          type="boolean",
 *          property="featured",
 *          description="Whether the game is featured"
 *     )
 * )
 */
interface GameInterface extends
    SoftDeleteInterface,
    StandardDateInterface,
    MetaDataInterface,
    FlagInterface,
    UriCollectionAwareInterface,
    SortableInterface
{
    const GAME_GLOBAL      = 1;  // Global games are visible to all
    const GAME_FEATURED    = 2;  // Featured games are ones that appear higher in the game list
    const GAME_COMING_SOON = 4;  // Coming soon games are previews of new and exciting games
    const GAME_UNITY       = 8;  // Flags the game as a unity based game
    const GAME_DESKTOP     = 16; // Flags the game for desktop only

    const URL_GAME   = 'game_url';
    const URL_THUMB  = 'thumb_url';
    const URL_BANNER = 'banner_url';

    /**
     * Exchanges an array to hydrate the object
     *
     * @param array $array
     *
     * @return GameInterface
     */
    public function exchangeArray(array $array): GameInterface;

    /**
     * Return an array representation of the object
     *
     * @return array
     */
    public function getArrayCopy(): array;

    /**
     * Returns the Id for the game
     *
     * @return string
     */
    public function getGameId(): string;

    /**
     * Sets the Id for the game
     *
     * @param string $gameId
     *
     * @return GameInterface
     */
    public function setGameId(string $gameId): GameInterface;

    /**
     * Gets the title of the game
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Sets the title of the game
     *
     * @param string $title
     *
     * @return GameInterface
     */
    public function setTitle(string $title): GameInterface;

    /**
     * Gets the description of the game
     *
     * @return string
     */
    public function getDescription(): string;

    /**
     * Sets the description of the game
     *
     * @param string $description
     *
     * @return GameInterface
     */
    public function setDescription(string $description): GameInterface;

    /**
     * Weather the game is coming soon or not
     *
     * @return boolean
     */
    public function isComingSoon(): bool;

    /**
     * Helps check if the game is a global game or not
     *
     * @return bool
     */
    public function isGlobal(): bool;

    /**
     * Helps check if the game is a featured game or not
     *
     * @return bool
     */
    public function isFeatured(): bool;

    /**
     * Helps check if the game can only be played on desktop
     *
     * @return bool
     */
    public function isDesktop(): bool;

    /**
     * Helps check if the game is a unity game
     *
     * @return bool
     */
    public function isUnity(): bool;
}
