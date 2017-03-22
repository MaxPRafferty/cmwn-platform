<?php

namespace Game;

use Application\Utils\Date\SoftDeleteTrait;
use Application\Utils\Date\StandardDatesTrait;
use Application\Utils\Flags\FlagTrait;
use Application\Utils\Meta\MetaDataTrait;
use Application\Utils\PropertiesTrait;
use Application\Utils\Sort\SortableTrait;
use Application\Utils\Uri\UriCollectionAwareTrait;
use Ramsey\Uuid\Uuid;
use Zend\Filter\StaticFilter;
use Zend\Filter\Word\UnderscoreToCamelCase;
use Zend\Stdlib\ArraySerializableInterface;

/**
 * Game Model
 */
class Game implements ArraySerializableInterface, GameInterface
{
    use StandardDatesTrait,
        MetaDataTrait,
        PropertiesTrait,
        FlagTrait,
        UriCollectionAwareTrait,
        SortableTrait,
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
    protected $title = '';

    /**
     * @var string
     */
    protected $description = '';

    protected static $flagMap = [
        'desktop'     => self::GAME_DESKTOP,
        'featured'    => self::GAME_FEATURED,
        'coming_soon' => self::GAME_COMING_SOON,
        'global'      => self::GAME_GLOBAL,
        'unity'       => self::GAME_UNITY,
    ];

    /**
     * Game constructor.
     *
     * @param array|null $options
     */
    public function __construct(array $options = null)
    {
        if (!empty($options)) {
            $this->exchangeArray($options);
        }
    }

    /**
     * @inheritdoc
     */
    public function exchangeArray(array $array): GameInterface
    {
        foreach ($array as $key => $value) {
            // set using the method
            $method = 'set' . ucfirst(StaticFilter::execute($key, UnderscoreToCamelCase::class));
            if (method_exists($this, $method)) {
                $this->{$method}($value);
                continue;
            }

            // check for flag keys (comes in this way from the API)
            // key is not a flag
            if (!array_key_exists($key, static::$flagMap)) {
                continue;
            }

            // get the flag from the array
            $flag = static::$flagMap[$key];

            // toggle the flag to desired state
            if ($value && !$this->hasFlag($flag)) {
                $this->toggleFlag($flag);
            } elseif (!$value && $this->hasFlag($flag)) {
                $this->toggleFlag($flag);
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
            'game_id'     => $this->getGameId(),
            'title'       => $this->getTitle(),
            'description' => $this->getDescription(),
            'created'     => $this->formatCreated('Y-m-d H:i:s'),
            'updated'     => $this->formatUpdated('Y-m-d H:i:s'),
            'deleted'     => $this->formatDeleted('Y-m-d H:i:s'),
            'meta'        => $this->getMeta(),
            'coming_soon' => $this->isComingSoon(),
            'global'      => $this->isGlobal(),
            'featured'    => $this->isFeatured(),
            'unity'       => $this->isUnity(),
            'desktop'     => $this->isDesktop(),
            'uris'        => $this->getUris(),
            'sort_order'  => $this->getSortOrder(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getGameId(): string
    {
        if (empty($this->gameId)) {
            $this->setGameId(Uuid::uuid1());
        }

        return $this->gameId;
    }

    /**
     * @inheritdoc
     */
    public function setGameId(string $gameId): GameInterface
    {
        $this->gameId = $gameId;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @inheritdoc
     */
    public function setTitle(string $title): GameInterface
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @inheritdoc
     */
    public function setDescription(string $description): GameInterface
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isGlobal(): bool
    {
        return $this->hasFlag(static::GAME_GLOBAL);
    }

    /**
     * @inheritdoc
     */
    public function isComingSoon(): bool
    {
        return $this->hasFlag(static::GAME_COMING_SOON);
    }

    /**
     * @inheritDoc
     */
    public function isFeatured(): bool
    {
        return $this->hasFlag(static::GAME_FEATURED);
    }

    /**
     * @inheritDoc
     */
    public function isDesktop(): bool
    {
        return $this->hasFlag(static::GAME_DESKTOP);
    }

    /**
     * @inheritDoc
     */
    public function isUnity(): bool
    {
        return $this->hasFlag(static::GAME_UNITY);
    }

}
