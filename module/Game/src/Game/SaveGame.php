<?php

namespace Game;

use Application\Utils\Date\DateCreatedTrait;
use Game\Exception\RuntimeException;
use User\UserInterface;
use Zend\Filter\StaticFilter;
use Zend\Filter\Word\UnderscoreToCamelCase;
use Zend\Json\Json;

/**
 * A Class that represents users save game data
 */
class SaveGame implements SaveGameInterface
{
    use DateCreatedTrait;

    /**
     * @var string
     */
    protected $gameId = '';

    /**
     * @var string
     */
    protected $userId = '';

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var string
     */
    protected $version = '';

    /**
     * SaveGame constructor.
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
     * @inheritdoc
     */
    public function exchangeArray(array $array): SaveGameInterface
    {
        foreach ($array as $key => $value) {
            $method = 'set' . ucfirst(StaticFilter::execute($key, UnderscoreToCamelCase::class));
            if (method_exists($this, $method)) {
                $this->{$method}($value);
            }
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getArrayCopy(): array
    {
        return [
            'game_id' => $this->getGameId(),
            'user_id' => $this->getUserId(),
            'data'    => $this->getData(),
            'created' => $this->formatCreated('Y-m-d H:i:s'),
            'version' => $this->getVersion(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getGameId(): string
    {
        return $this->gameId;
    }

    /**
     * @inheritdoc
     */
    public function setGameId(string $gameId): SaveGameInterface
    {
        $this->gameId = $gameId;

        return $this;
    }

    /**
     * Helps set the game Id from a game
     *
     * @param GameInterface $game
     *
     * @return SaveGameInterface
     */
    public function setGameIdFromGame(GameInterface $game): SaveGameInterface
    {
        $this->setGameId($game->getGameId());

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @inheritdoc
     */
    public function setUserId(string $userId): SaveGameInterface
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Helps set the userId from a user
     *
     * @param UserInterface $user
     *
     * @return SaveGameInterface
     */
    public function setUserIdFromUser(UserInterface $user): SaveGameInterface
    {
        $this->setUserId($user->getUserId());

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function setData($gameData): SaveGameInterface
    {
        if (is_string($gameData)) {
            $gameData = Json::decode($gameData, Json::TYPE_ARRAY);
        }

        if (!is_array($gameData)) {
            throw new RuntimeException('Data for game MUST be an array or Json string');
        }

        $this->data = $gameData;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @inheritdoc
     */
    public function setVersion(string $version): SaveGameInterface
    {
        $this->version = $version;

        return $this;
    }
}
