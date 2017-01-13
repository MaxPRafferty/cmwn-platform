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
 * Class Feed
 * @package Feed
 */
class Feed extends \ArrayObject implements FeedInterface
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
     * @var String | mixed
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
    public function exchangeArray($array = [])
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
    public function getArrayCopy()
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
     * @return string
     */
    public function getFeedId()
    {
        return $this->feedId;
    }

    /**
     * @param string $feedId
     */
    public function setFeedId($feedId)
    {
        $this->feedId = $feedId;
    }

    /**
     * @return UserInterface|string|null
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param UserInterface|string|null $sender
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
    }

    /**
     * @return String
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param String $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed|String
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed|String $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param string $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @return \DateTime
     */
    public function getPosted()
    {
        return $this->posted;
    }

    /**
     * @param \DateTime $posted
     */
    public function setPosted($posted)
    {
        $this->posted = $posted;
    }

    /**
     * @return int
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * @param int $visibility
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;
    }

    /**
     * @return String
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param String $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getTypeVersion()
    {
        return $this->typeVersion;
    }

    /**
     * @param string $typeVersion
     */
    public function setTypeVersion($typeVersion)
    {
        $this->typeVersion = $typeVersion;
    }
}
