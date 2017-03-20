<?php

namespace Game;

use Application\Utils\Date\SoftDeleteTrait;
use Application\Utils\Date\StandardDatesTrait;
use Application\Utils\Flags\FlagTrait;
use Application\Utils\Meta\MetaDataTrait;
use Application\Utils\PropertiesTrait;
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
            'uris'        => $this->getUris(),
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
}
