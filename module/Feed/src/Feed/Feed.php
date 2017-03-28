<?php

namespace Feed;

use Application\Utils\Date\DateCreatedTrait;
use Application\Utils\Date\DateDeletedTrait;
use Application\Utils\Date\DateUpdatedTrait;
use Application\Utils\MetaDataTrait;
use User\UserInterface;
use Zend\Filter\StaticFilter;
use Zend\Filter\Word\UnderscoreToCamelCase;

/**
 * Implementation class for feed interface
 */
class Feed implements FeedInterface
{
    use DateCreatedTrait;
    use DateUpdatedTrait;
    use DateDeletedTrait;
    use MetaDataTrait;

    /**
     * @var string
     */
    protected $feedId;

    /**
     * @var UserInterface
     */
    protected $sender;

    /**
     * @var String
     */
    protected $title;

    /**
     * @var String
     */
    protected $message;

    /**
     * @var string
     */
    protected $priority;

    /**
     * @var \DateTime
     */
    protected $posted;

    /**
     * @var integer
     */
    protected $visibility;

    /**
     * @var String
     */
    protected $type;

    /**
     * @var string
     */
    protected $typeVersion;

    /**
     * Feed constructor.
     * @param array $array
     */
    public function __construct(array $array = [])
    {
        $this->exchangeArray($array);
    }

    /**
     * @inheritdoc
     */
    public function exchangeArray(array $array = [])
    {
        $defaults = [
            'feed_id'      => null,
            'sender'       => null,
            'title'        => null,
            'message'      => null,
            'priority'     => null,
            'posted'       => null,
            'visibility'   => null,
            'type'         => null,
            'type_version' => null,
            'meta'         => [],
            'created'      => null,
            'updated'      => null,
            'deleted'      => null,
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
    public function getArrayCopy() : array
    {
        return [
            'feed_id'      => $this->getFeedId(),
            'sender'       => $this->getSender(),
            'title'        => $this->getTitle(),
            'message'      => $this->getMessage(),
            'priority'     => $this->getPriority(),
            'posted'       => $this->getPosted(),
            'visibility'   => $this->getVisibility(),
            'type'         => $this->getType(),
            'type_version' => $this->getTypeVersion(),
            'meta'         => $this->getMeta(),
            'created'      => $this->getCreated() !== null ? $this->getCreated()->format("Y-m-d H:i:s") : null,
            'updated'      => $this->getUpdated() !== null ? $this->getUpdated()->format("Y-m-d H:i:s") : null,
            'deleted'      => $this->getDeleted() !== null ? $this->getDeleted()->format("Y-m-d H:i:s") : null,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getFeedId() : string
    {
        return $this->feedId;
    }

    /**
     * @inheritdoc
     */
    public function setFeedId(string $feedId = null)
    {
        $this->feedId = (string) $feedId;
    }

    /**
     * @inheritdoc
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @inheritdoc
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
    }

    /**
     * @inheritdoc
     */
    public function getTitle() : string
    {
        return $this->title;
    }

    /**
     * @inheritdoc
     */
    public function setTitle(string $title = null)
    {
        $this->title = (string) $title;
    }

    /**
     * @inheritdoc
     */
    public function getMessage() : string
    {
        return $this->message;
    }

    /**
     * @inheritdoc
     */
    public function setMessage(string $message = null)
    {
        $this->message = (string) $message;
    }

    /**
     * @inheritdoc
     */
    public function getPriority() : string
    {
        return $this->priority;
    }

    /**
     * @inheritdoc
     */
    public function setPriority(string $priority = null)
    {
        $this->priority = (string) $priority;
    }

    /**
     * @inheritdoc
     */
    public function getPosted() : string
    {
        return $this->posted;
    }

    /**
     * @inheritdoc
     */
    public function setPosted(string $posted = null)
    {
        $this->posted = (string) $posted;
    }

    /**
     * @inheritdoc
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * @inheritdoc
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;
    }

    /**
     * @inheritdoc
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * @inheritdoc
     */
    public function setType(string $type = null)
    {
        $this->type = (string) $type;
    }

    /**
     * @inheritdoc
     */
    public function getTypeVersion() : string
    {
        return $this->typeVersion;
    }

    /**
     * @inheritdoc
     */
    public function setTypeVersion(string $typeVersion = null)
    {
        $this->typeVersion = (string) $typeVersion;
    }
}
