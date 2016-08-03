<?php

namespace Group;

use Application\Utils\Date\DateCreatedTrait;
use Application\Utils\Date\DateDeletedTrait;
use Application\Utils\Date\DateUpdatedTrait;
use Application\Utils\MetaDataTrait;
use Application\Utils\PropertiesTrait;
use Application\Utils\SoftDeleteInterface;
use Org\OrganizationInterface;
use Zend\Filter\StaticFilter;
use Zend\Stdlib\ArraySerializableInterface;

/**
 * Class Group
 * @package Group
 */
class Group implements SoftDeleteInterface, GroupInterface, ArraySerializableInterface
{
    use DateCreatedTrait;
    use DateUpdatedTrait;
    use DateDeletedTrait;
    use PropertiesTrait;
    use MetaDataTrait;

    /**
     * @var string
     */
    protected $groupId;

    /**
     * @var string
     */
    protected $organizationId;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var int
     */
    protected $head = 1;

    /**
     * @var int
     */
    protected $tail = 2;

    /**
     * @var int
     */
    protected $depth = 0;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $externalId;

    /**
     * @var null|string
     */
    protected $parentId;

    /**
     * @var null|string
     */
    protected $networkId;

    /**
     * Group constructor.
     * @param array|null $options
     */
    public function __construct(array $options = null)
    {
        if ($options !== null) {
            $this->exchangeArray($options);
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getTitle();
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
            'group_id'        => null,
            'organization_id' => null,
            'title'           => null,
            'description'     => null,
            'type'            => null,
            'meta'            => [],
            'head'            => null,
            'tail'            => null,
            'depth'           => null,
            'created'         => null,
            'updated'         => null,
            'deleted'         => null,
            'external_id'     => null,
            'parent_id'       => null,
            'network_id'      => null,
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
     * Return an array representation of the object
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return [
            'group_id'        => $this->getGroupId(),
            'organization_id' => $this->getOrganizationId(),
            'title'           => $this->getTitle(),
            'description'     => $this->getDescription(),
            'type'            => $this->getType(),
            'meta'            => $this->getMeta(),
            'head'            => $this->getHead(),
            'tail'            => $this->getTail(),
            'depth'           => $this->getDepth(),
            'external_id'     => $this->getExternalId(),
            'created'         => $this->getCreated() !== null ? $this->getCreated()->format(\DateTime::ISO8601) : null,
            'updated'         => $this->getUpdated() !== null ? $this->getUpdated()->format(\DateTime::ISO8601) : null,
            'deleted'         => $this->getDeleted() !== null ? $this->getDeleted()->format(\DateTime::ISO8601) : null,
            'parent_id'       => $this->getParentId(),
            'network_id'      => $this->getNetworkId(),
        ];
    }

    /**
     * @return string
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * @param string $groupId
     * @return Group
     */
    public function setGroupId($groupId)
    {
        $this->groupId = (string) $groupId;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrganizationId()
    {
        return $this->organizationId;
    }

    /**
     * @param string|OrganizationInterface $organizationId
     * @return Group
     */
    public function setOrganizationId($organizationId)
    {
        $organizationId = $organizationId instanceof OrganizationInterface
            ? $organizationId->getOrgId()
            : $organizationId;

        $this->organizationId = $organizationId;

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
     * @return Group
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
     * @return Group
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return int
     */
    public function getHead()
    {
        return $this->head;
    }

    /**
     * @param int $head
     *
     * @return Group
     */
    public function setHead($head)
    {
        $this->head = abs($head);
        return $this;
    }

    /**
     * @return int
     */
    public function getTail()
    {
        return $this->tail;
    }

    /**
     * @param int $tail
     *
     * @return Group
     */
    public function setTail($tail)
    {
        $this->tail = abs($tail);
        return $this;
    }

    /**
     * @return int
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * @param int $depth
     * @return Group
     */
    public function setDepth($depth)
    {
        $this->depth = abs($depth);
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
     * @return Group
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRoot()
    {
        return $this->head === 1;
    }

    /**
     * @return bool
     */
    public function hasChildren()
    {
        if ($this->head === 0 || $this->tail === 0) {
            return false;
        }

        return $this->head !== ($this->tail - 1);
    }

    /**
     * Gets the users Identifier for this group
     *
     * @return null|string
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * Sets the users identifier for this group
     *
     * @param $externalId
     * @return string
     */
    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
    }

    /**
     * @return null|string
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @param null|string $parentId
     */
    public function setParentId($parentId)
    {
        $parentId = $parentId instanceof static ? $parentId->getGroupId() : $parentId;
        $this->parentId = $parentId;
    }

    /**
     * @inheritDoc
     */
    public function getNetworkId()
    {
        return $this->networkId;
    }

    /**
     * @inheritDoc
     */
    public function setNetworkId($networkId)
    {
        $this->networkId = $networkId;
    }
}
