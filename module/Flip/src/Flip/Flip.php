<?php

namespace Flip;

use BaconStringUtils\Slugifier;
use Feed\FeedableTrait;
use Feed\FeedInterface;
use Zend\Filter\StaticFilter;
use Zend\Filter\Word\UnderscoreToCamelCase;

/**
 * The Basic flip
 */
class Flip implements FlipInterface
{
    use FeedableTrait;

    /**
     * @var string
     */
    protected $flipId;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * Flip constructor.
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
    public function __toString()
    {
        return (string)$this->getTitle();
    }

    /**
     * @inheritdoc
     */
    public function exchangeArray(array $array)
    {
        $defaults = [
            'flip_id'     => '',
            'title'       => '',
            'description' => '',
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
     * @inheritdoc
     */
    public function getArrayCopy(): array
    {
        return [
            'flip_id'     => $this->getFlipId(),
            'title'       => $this->getTitle(),
            'description' => $this->getDescription(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getFlipId(): string
    {
        if (empty($this->flipId)) {
            $slug = new Slugifier();
            $this->setFlipId($slug->slugify($this->getTitle()));
        }

        return (string)$this->flipId;
    }

    /**
     * @inheritdoc
     */
    public function setFlipId(string $flipId): FlipInterface
    {
        $this->flipId = $flipId;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTitle(): string
    {
        return (string)$this->title;
    }

    /**
     * @inheritdoc
     */
    public function setTitle(string $title): FlipInterface
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDescription(): string
    {
        return (string)$this->description;
    }

    /**
     * @inheritdoc
     */
    public function setDescription(string $description): FlipInterface
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getFeedMessage(): string
    {
        return 'Flip Earned';
    }

    /**
     * @inheritdoc
     */
    public function getFeedMeta(): array
    {
        return ['flip_id' => $this->getFlipId()];
    }

    /**
     * @inheritdoc
     */
    public function getFeedVisiblity(): int
    {
        return FeedInterface::VISIBILITY_FRIENDS;
    }

    /**
     * @inheritdoc
     */
    public function getFeedType(): string
    {
        return FeedInterface::TYPE_FLIP;
    }

    /**
     * @inheritdoc
     */
    public function getFeedTitle(): string
    {
        return 'You have earned a new flip';
    }

    /**
     * @inheritdoc
     */
    public function getFeedPriority(): string
    {
        return '5';
    }
}
