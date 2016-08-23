<?php

namespace Api\V1\Rest\Feed;

use Api\V1\Rest\Image\ImageEntity;
use Zend\Filter\StaticFilter;

/**
 * Class FeedEntity
 * @package Api\V1\Rest\Feed
 */
class FeedEntity extends \ArrayObject
{
    /**
     * @var String
     */
    protected $feedId;

    /**
     * @var SenderEntity
     */
    protected $sender;

    /**
     * @var String
     */
    protected $header;

    /**
     * @var String | mixed
     */
    protected $message;

    /**
     * @var ImageEntity
     */
    protected $image;

    /**
     * @var \DateTime
     */
    protected $created;

    /**
     * @var String
     */
    protected $type;

    /**
     * @var String
     */
    protected $link;

    /**
     * FeedEntity constructor.
     * @param array|null $array
     */
    public function __construct(array $array = null)
    {
        if ($array !== null) {
            $this->exchangeArray($array);
        }
    }

    /**
     * @inheritdoc
     */
    public function exchangeArray(array $array)
    {
        $defaults = [
            'feed_id' => null,
            'sender' => null,
            'header' => null,
            'message' => null,
            'image' => null,
            'created' => null,
            'type' => null,
            'link' => null,
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
     * @return array
     */
    public function getArrayCopy()
    {
        return [
            'feed_id' => $this->getFeedId(),
            'sender' => $this->getSender(),
            'header' => $this->getHeader(),
            'message' => $this->getMessage(),
            'image' => $this->getImage(),
            'created' => $this->getCreated(),
            'type' => $this->getType(),
            'link' => $this->getLink(),
        ];
    }

    /**
     * @return String
     */
    public function getFeedId()
    {
        return $this->feedId;
    }

    /**
     * @param String $feedId
     */
    public function setFeedId($feedId)
    {
        $this->feedId = $feedId;
    }

    /**
     * @return SenderEntity
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param SenderEntity $sender
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
    }

    /**
     * @return String
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param String $header
     */
    public function setHeader($header)
    {
        $this->header = $header;
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
     * @return ImageEntity
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param ImageEntity $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
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
     * @return String
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param String $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }
}
