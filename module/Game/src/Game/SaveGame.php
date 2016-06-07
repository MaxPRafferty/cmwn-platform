<?php

namespace Game;

use Application\Utils\Date\DateCreatedTrait;
use Application\Utils\PropertiesTrait;
use Zend\Filter\StaticFilter;

/**
 * Class SaveGame
 */
class SaveGame implements SaveGameInterface
{
    use DateCreatedTrait;
    use PropertiesTrait;

    /**
     * @var string
     */
    protected $gameId;

    /**
     * @var string
     */
    protected $userId;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * Save Game constructor.
     *
     * @param array|null $options
     */
    public function __construct(array $options = null)
    {
        if ($options !== null) {
            $this->exchangeArray($options);
        }
    }

    /**
     * Exchange internal values from provided array
     *
     * @param  array $array
     * @return void
     */
    public function exchangeArray(array $array)
    {
        $defaults = [
            'game_id' => null,
            'user_id' => null,
            'data'    => [],
            'created' => null,
        ];

        $array = array_merge($defaults, $array);

        foreach ($array as $key => $value) {
            $method = 'set' . ucfirst(StaticFilter::execute($key, 'Word\UnderscoreToCamelCase'));
            if (method_exists($this, $method)) {
                $this->{$method}($value);
            }
        }
    }
    /**
     * Return an array representation of the object
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return [
            'game_id' => $this->getGameId(),
            'user_id' => $this->getUserId(),
            'data'    => $this->getData(),
            'created' => $this->getCreated() !== null ? $this->getCreated()->format(\DateTime::ISO8601) : null,
        ];
    }

    /**
     * Gets the Game Id
     *
     * @return string
     */
    public function getGameId()
    {
        return $this->gameId;
    }

    /**
     * Sets the Game Id
     *
     * @param string $gameId
     */
    public function setGameId($gameId)
    {
        $this->gameId = $gameId;
    }

    /**
     * Gets the UserId
     *
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Sets the User Id
     *
     * @param string $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Gets the Game Data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Saves the Game Data
     *
     * @param array $gameData
     */
    public function setData(array $gameData)
    {
        $this->data = $gameData;
    }
}
