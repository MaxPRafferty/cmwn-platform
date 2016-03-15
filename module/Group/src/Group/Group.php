<?php

namespace Group;

use Application\Utils\Date\DateCreatedTrait;
use Application\Utils\Date\DateDeletedTrait;
use Application\Utils\Date\DateUpdatedTrait;
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
     * @var array
     */
    protected $meta = [];

    /**
     * @var int
     */
    protected $left = 1;

    /**
     * @var int
     */
    protected $right = 2;

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
            'group_id'        => null,
            'organization_id' => null,
            'title'           => null,
            'description'     => null,
            'type'            => null,
            'meta'            => [],
            'left'            => null,
            'right'           => null,
            'depth'           => null,
            'created'         => null,
            'updated'         => null,
            'deleted'         => null,
            'external_id'     => null
        ];

        $array = array_merge($defaults, $array);

        $array['left'] = isset($array['lft']) ? $array['lft'] : $array['left'];
        $array['right'] = isset($array['rgt']) ? $array['rgt'] : $array['right'];

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
            'left'            => $this->getLeft(),
            'right'           => $this->getRight(),
            'depth'           => $this->getDepth(),
            'external_id'     => $this->getExternalId(),
            'created'         => $this->getCreated() !== null ? $this->getCreated()->format(\DateTime::ISO8601) : null,
            'updated'         => $this->getUpdated() !== null ? $this->getUpdated()->format(\DateTime::ISO8601) : null,
            'deleted'         => $this->getDeleted() !== null ? $this->getDeleted()->format(\DateTime::ISO8601) : null,
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
     * @return array
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @param array $meta
     * @return Group
     */
    public function setMeta($meta)
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * @return int
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * @param int $left
     * @return Group
     */
    public function setLeft($left)
    {
        $this->left = abs($left);
        return $this;
    }

    /**
     * @return int
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * @param int $right
     * @return Group
     */
    public function setRight($right)
    {
        $this->right = abs($right);
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
        return $this->left === 1;
    }

    /**
     * @return bool
     */
    public function hasChildren()
    {
        if ($this->left === 0 || $this->right === 0) {
            return false;
        }

        return $this->left === ($this->right - 1);
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
}
