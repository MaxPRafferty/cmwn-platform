<?php

namespace Game;

use Application\Utils\Date\SoftDeleteTrait;
use Application\Utils\Date\StandardDatesTrait;
use Application\Utils\Meta\MetaDataTrait;
use Application\Utils\PropertiesTrait;
use Zend\Filter\StaticFilter;
use Zend\Filter\Word\UnderscoreToCamelCase;
use Zend\Stdlib\ArraySerializableInterface;

/**
 * Class Game
 *
 * Game Model
 */
class Game implements ArraySerializableInterface, GameInterface
{
    use StandardDatesTrait,
        MetaDataTrait,
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
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var bool
     */
    protected $comingSoon = false;

    /**
     * @var bool
     */
    protected $global = false;

    /**
     * Game constructor.
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
     *
     * @return void
     */
    public function exchangeArray(array $array)
    {
        $defaults = [
            'game_id'     => null,
            'title'       => null,
            'description' => null,
            'meta'        => [],
            'created'     => null,
            'updated'     => null,
            'deleted'     => null,
            'coming_soon' => false,
            'global'      => false,
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
            'game_id'     => $this->getGameId(),
            'title'       => $this->getTitle(),
            'description' => $this->getDescription(),
            'created'     => $this->getCreated() !== null ? $this->getCreated()->format(\DateTime::ISO8601) : null,
            'updated'     => $this->getUpdated() !== null ? $this->getUpdated()->format(\DateTime::ISO8601) : null,
            'deleted'     => $this->getDeleted() !== null ? $this->getDeleted()->format(\DateTime::ISO8601) : null,
            'coming_soon' => $this->isComingSoon(),
            'meta'        => $this->getMeta(),
            'global'      => $this->isGlobal(),
        ];
    }

    /**
     * @return boolean
     */
    public function isComingSoon()
    {
        return $this->comingSoon;
    }

    /**
     * @param boolean $comingSoon
     */
    public function setComingSoon($comingSoon)
    {
        $this->comingSoon = (bool)$comingSoon;
    }

    /**
     * @return string
     */
    public function getGameId()
    {
        return $this->gameId;
    }

    /**
     * @param string $gameId
     *
     * @return Game
     */
    public function setGameId($gameId)
    {
        $this->gameId = $gameId;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Game
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Game
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return mixed
     */
    public function isGlobal()
    {
        return $this->global;
    }

    /**
     * @param mixed $global
     */
    public function setGlobal($global)
    {
        $this->global = (bool)$global;
    }
}
