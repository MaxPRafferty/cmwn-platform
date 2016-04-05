<?php

namespace Asset;

use Application\Utils\Date\DateCreatedTrait;
use Application\Utils\Date\DateDeletedTrait;
use Application\Utils\Date\DateUpdatedTrait;
use Application\Utils\PropertiesTrait;
use Application\Utils\SoftDeleteInterface;
use Zend\Filter\StaticFilter;
use Zend\Stdlib\ArraySerializableInterface;

/**
 * Class Image
 * @package Asset
 */
class Image implements ArraySerializableInterface, SoftDeleteInterface, ImageInterface
{
    use DateCreatedTrait;
    use DateDeletedTrait;
    use DateUpdatedTrait;
    use PropertiesTrait;

    /**
     * @var string
     */
    protected $imageId;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var bool
     */
    protected $moderated = false;

    /**
     * Image constructor.
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
            'image_id'     => null,
            'url'          => null,
            'is_moderated' => false,
            'type'         => null,
            'created'      => null,
            'updated'      => null,
            'deleted'      => null,
        ];

        $array = array_merge($defaults, $array);

        if (!isset($array['moderated']) && isset($array['is_moderated'])) {
            $array['moderated'] = $array['is_moderated'];
        }

        // moderation_status will always take precedent
        if (isset($array['moderation_status'])) {
            $array['moderated'] = $array['moderation_status'];
        }

        foreach ($array as $key => $value) {
            $method = 'set' . ucfirst(StaticFilter::execute($key, 'Word\UnderscoreToCamelCase'));
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
            'image_id'     => $this->getImageId(),
            'url'          => $this->getUrl(),
            'is_moderated' => $this->isModerated(),
            'type'         => $this->getType(),
            'created'      => $this->getCreated() !== null ? $this->getCreated()->format(\DateTime::ISO8601) : null,
            'updated'      => $this->getUpdated() !== null ? $this->getUpdated()->format(\DateTime::ISO8601) : null,
            'deleted'      => $this->getDeleted() !== null ? $this->getDeleted()->format(\DateTime::ISO8601) : null,
        ];
    }

    /**
     * @return boolean
     */
    public function isModerated()
    {
        return $this->moderated;
    }

    /**
     * @return boolean
     */
    public function isApproved()
    {
        return $this->moderated === static::IMAGE_APPROVED;
    }

    /**
     * @return bool
     */
    public function isRejected()
    {
        return $this->moderated === static::IMAGE_REJECTED;
    }

    /**
     * @param boolean $moderated
     * @return Image
     */
    public function setModerated($moderated)
    {
        $this->moderated = (bool) $moderated;
        return $this;
    }

    /**
     * @return string
     */
    public function getImageId()
    {
        return $this->imageId;
    }

    /**
     * @param string $imageId
     * @return Image
     */
    public function setImageId($imageId)
    {
        $this->imageId = $imageId;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return Image
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Image
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Transform a status to its value
     *
     * @param $status
     * @return int
     */
    public static function statusToNumber($status)
    {
        $statuses = [
            static::IMAGE_APPROVED => 'approved',
            static::IMAGE_PENDING  => 'pending',
            static::IMAGE_REJECTED => 'rejected'
        ];

        $code = array_search($status, $statuses);
        if ($code === false) {
            throw new \InvalidArgumentException('Invalid status code: ' . $status);
        }

        return $code;
    }
}
