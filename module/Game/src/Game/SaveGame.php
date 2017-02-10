<?php

namespace Game;

use Application\Utils\Date\SoftDeleteTrait;
use Application\Utils\Date\StandardDatesTrait;
use Application\Utils\PropertiesTrait;
use Zend\Filter\StaticFilter;
use Zend\Filter\Word\UnderscoreToCamelCase;
use Zend\Json\Json;

/**
 * Class SaveGame
 */
class SaveGame implements SaveGameInterface
{
    use StandardDatesTrait,
        PropertiesTrait,
        SoftDeleteTrait {
        SoftDeleteTrait::getDeleted insteadof StandardDatesTrait;
        SoftDeleteTrait::setDeleted insteadof StandardDatesTrait;
        SoftDeleteTrait::formatDeleted insteadof StandardDatesTrait;
    }
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
     * @var string
     */
    protected $version;

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
            'version' => null,
        ];

        $array = array_merge($defaults, $array);

        foreach ($array as $key => $value) {
            $method = 'set' . ucfirst(StaticFilter::execute($key, UnderscoreToCamelCase::class));
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
            'created' => $this->formatCreated(\DateTime::ISO8601),
            'version' => $this->getVersion(),
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
     * @param array|string $gameData
     */
    public function setData($gameData)
    {
        if (is_string($gameData)) {
            $gameData = Json::decode($gameData, Json::TYPE_ARRAY);
        }

        $this->data = !is_array($gameData) ? [$gameData] : $gameData;
    }

    /**
     * Returns back the version that this game was saved at
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Sets the version of the game data
     *
     * @param $version
     *
     * @return $this
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }
}
