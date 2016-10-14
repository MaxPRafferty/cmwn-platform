<?php

namespace Api\V1\Rest\Feed;

use Api\V1\Rest\Image\ImageEntity;
use Zend\Filter\StaticFilter;

/**
 * Class SenderEntity
 * @package Api\V1\Rest\Feed
 */
class SenderEntity extends \ArrayObject
{
    /**
     * @var String
     */
    protected $senderId;

    /**
     * @var String
     */
    protected $senderUserName;

    /**
     * @var String
     */
    protected $firstName;

    /**
     * @var String
     */
    protected $lastName;

    /**
     * @var ImageEntity
     */
    protected $image;

    /**
     * @inheritdoc
     */
    public function getArrayCopy()
    {
        return [
            'sender_id' => $this->getSenderId(),
            'sender_user_name' => $this->getSenderUserName(),
            'first_name' => $this->getFirstName(),
            'last_name' => $this->getLastName(),
            'image' => $this->getImage(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function exchangeArray($array)
    {
        $defaults = [
            'sender_id' => null,
            'sender_user_name' => null,
            'first_name' => null,
            'last_name' => null,
            'image' => null,
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
     * @return mixed
     */
    public function getSenderId()
    {
        return $this->senderId;
    }

    /**
     * @param mixed $senderId
     */
    public function setSenderId($senderId)
    {
        $this->senderId = $senderId;
    }

    /**
     * @return mixed
     */
    public function getSenderUserName()
    {
        return $this->senderUserName;
    }

    /**
     * @param mixed $senderUserName
     */
    public function setSenderUserName($senderUserName)
    {
        $this->senderUserName = $senderUserName;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }
}
