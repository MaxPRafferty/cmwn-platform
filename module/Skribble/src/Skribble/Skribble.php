<?php

namespace Skribble;

use Application\Utils\Date\DateCreatedTrait;
use Application\Utils\Date\DateDeletedTrait;
use Application\Utils\Date\DateUpdatedTrait;
use Skribble\Rule\SkribbleRules;
use User\UserInterface;
use Zend\Filter\StaticFilter;
use Zend\Json\Json;

/**
 * Class Skribble
 */
class Skribble implements SkribbleInterface
{
    use DateDeletedTrait;
    use DateCreatedTrait;
    use DateUpdatedTrait;

    /**
     * @var string
     */
    protected $skirbbleId;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var string
     */
    protected $friendTo;

    /**
     * @var string
     */
    protected $createdBy;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var int
     */
    protected $version = self::CURRENT_VERSION;

    /**
     * @var SkribbleRules
     */
    protected $rules;

    /**
     * @var bool
     */
    protected $read = false;

    /**
     * Skribble constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->rules = new SkribbleRules();
        $this->exchangeArray($options);
    }

    /**
     * Return an array representation of the object
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return [
            'skribble_id' => $this->getSkribbleId(),
            'version'     => $this->getVersion(),
            'url'         => $this->getUrl(),
            'created'     => $this->getCreated() !== null ? $this->getCreated()->format("Y-m-d H:i:s") : null,
            'updated'     => $this->getUpdated() !== null ? $this->getUpdated()->format("Y-m-d H:i:s") : null,
            'deleted'     => $this->getDeleted() !== null ? $this->getDeleted()->format("Y-m-d H:i:s") : null,
            'status'      => $this->getStatus(),
            'created_by'  => $this->getCreatedBy(),
            'friend_to'   => $this->getFriendTo(),
            'read'        => $this->isRead(),
            'rules'       => $this->getRules()->getArrayCopy(),
        ];
    }

    /**
     * Exchange internal values from provided array
     *
     * @param  array $array
     *
     *
     * @return void
     */
    public function exchangeArray(array $array)
    {
        $defaults = [
            'skribble_id' => null,
            'version'     => static::CURRENT_VERSION,
            'url'         => null,
            'created'     => null,
            'updated'     => null,
            'deleted'     => null,
            'status'      => static::STATUS_NOT_COMPLETE,
            'created_by'  => null,
            'friend_to'   => null,
            'read'        => false,
            'rules'       => [],
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
     * @return string
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param string $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        // Created by needs to be idempotent
        if ($this->createdBy !== null && !empty($createdBy)) {
            return;
        }

        $createdBy       = $createdBy instanceof UserInterface ? $createdBy->getUserId() : $createdBy;
        $this->createdBy = $createdBy;
    }

    /**
     * @return SkribbleRules
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @param SkribbleRules|string|array $rules
     */
    public function setRules($rules)
    {
        $rules = is_string($rules) ? Json::decode($rules, Json::TYPE_ARRAY) : $rules;
        $rules = is_array($rules) ? new SkribbleRules($rules) : $rules;

        if (!$rules instanceof SkribbleRules) {
            throw new \InvalidArgumentException(
                'Only arrays, Json or instances of SkribbleRules can be set for rules'
            );
        }

        $this->rules = $rules;
    }

    /**
     * @return string
     */
    public function getSkribbleId()
    {
        return $this->skirbbleId;
    }

    /**
     * @param string $skirbbleId
     */
    public function setSkribbleId($skirbbleId)
    {
        $this->skirbbleId = (string) $skirbbleId;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getFriendTo()
    {
        return $this->friendTo;
    }

    /**
     * @param string $friendTo
     */
    public function setFriendTo($friendTo)
    {
        // Friend to needs to be idempotent
        if ($this->friendTo !== null && !empty($friendTo)) {
            return;
        }

        $friendTo       = $friendTo instanceof UserInterface ? $friendTo->getUserId() : $friendTo;
        $this->friendTo = $friendTo;
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
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param int $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return boolean
     */
    public function isRead()
    {
        return $this->read;
    }

    /**
     * @param boolean $read
     */
    public function setRead($read)
    {
        $this->read = (bool)$read;
    }
}
