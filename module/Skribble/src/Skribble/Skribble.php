<?php

namespace Skribble;

use Application\Utils\Date\SoftDeleteTrait;
use Application\Utils\Date\StandardDatesTrait;
use Feed\FeedInterface;
use Skribble\Rule\SkribbleRules;
use User\UserInterface;
use Zend\Filter\StaticFilter;
use Zend\Filter\Word\UnderscoreToCamelCase;
use Zend\Json\Json;

/**
 * Class Skribble
 */
class Skribble implements SkribbleInterface
{
    use StandardDatesTrait,
        SoftDeleteTrait {
            SoftDeleteTrait::getDeleted insteadof StandardDatesTrait;
            SoftDeleteTrait::setDeleted insteadof StandardDatesTrait;
            SoftDeleteTrait::formatDeleted insteadof StandardDatesTrait;
    }
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
     * @inheritdoc
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
     * @inheritdoc
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
            $method = 'set' . ucfirst(StaticFilter::execute($key, UnderscoreToCamelCase::class));
            if (method_exists($this, $method)) {
                $this->{$method}($value);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function getSkribbleId()
    {
        return $this->skirbbleId;
    }

    /**
     * @inheritdoc
     */
    public function setSkribbleId($skirbbleId)
    {
        $this->skirbbleId = (string) $skirbbleId;
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @inheritdoc
     */
    public function getFriendTo()
    {
        return $this->friendTo;
    }

    /**
     * @inheritdoc
     */
    public function setFriendTo($friendTo)
    {
        // Friend to needs to be idempotent
        if (!empty($this->friendTo) && $this->getStatus() !== static::STATUS_NOT_COMPLETE) {
            return;
        }

        $friendTo       = $friendTo instanceof UserInterface ? $friendTo->getUserId() : $friendTo;
        $this->friendTo = $friendTo;
    }

    /**
     * @inheritdoc
     */
    public function getUrl()
    {
        if ($this->url === null && $this->getStatus() === static::STATUS_COMPLETE) {
            $this->setUrl(static::SKRIBBLE_BASE_URL . $this->getSkribbleId() . '.png');
        }

        return $this->url;
    }

    /**
     * @inheritdoc
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @inheritdoc
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @inheritdoc
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @inheritdoc
     */
    public function isRead()
    {
        return $this->read;
    }

    /**
     * @inheritdoc
     */
    public function setRead($read)
    {
        $this->read = (bool)$read;
    }

    /**
     * @inheritdoc
     */
    public function getFeedMessage(): string
    {
        return FeedInterface::MESSAGE_SKRIBBLE_RECEIVED;
    }

    /**
     * @inheritdoc
     */
    public function getFeedMeta(): array
    {
        return ['skribble_id' => $this->getSkribbleId()];
    }

    /**
     * @inheritdoc
     */
    public function getFeedVisiblity(): int
    {
        return FeedInterface::VISIBILITY_SELF;
    }

    /**
     * @inheritdoc
     */
    public function getFeedType(): string
    {
        return FeedInterface::TYPE_SKRIBBLE;
    }

    /**
     * @inheritdoc
     */
    public function getFeedTitle(): string
    {
        FeedInterface::TITLE_SKRIBBLE_RECEIVED;
    }
}
