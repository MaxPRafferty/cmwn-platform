<?php

namespace Group;

use Application\Utils\Date\SoftDeleteTrait;
use Application\Utils\Date\StandardDatesTrait;
use Application\Utils\Meta\MetaDataTrait;
use Application\Utils\PropertiesTrait;
use Application\Utils\Type\TypeTrait;
use Org\OrganizationInterface;
use Ramsey\Uuid\Uuid;
use Zend\Filter\StaticFilter;
use Zend\Filter\Word\UnderscoreToCamelCase;

/**
 * Defines a group
 */
class Group implements GroupInterface
{
    use StandardDatesTrait,
        MetaDataTrait,
        PropertiesTrait,
        TypeTrait,
        SoftDeleteTrait {
            SoftDeleteTrait::getDeleted insteadof StandardDatesTrait;
            SoftDeleteTrait::setDeleted insteadof StandardDatesTrait;
            SoftDeleteTrait::formatDeleted insteadof StandardDatesTrait;
        }

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
    protected $externalId;

    /**
     * @var null|string
     */
    protected $parentId;

    /**
     * @var string
     */
    protected $networkId;

    /**
     * Group constructor.
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
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getTitle();
    }

    /**
     * @inheritdoc
     */
    public function exchangeArray(array $array): GroupInterface
    {
        $defaults = [
            'group_id'        => null,
            'organization_id' => null,
            'title'           => null,
            'type'            => null,
            'head'            => 0,
            'tail'            => 0,
            'depth'           => 0,
        ];

        $array = array_merge($defaults, $array);

        foreach ($array as $key => $value) {
            $method = 'set' . ucfirst(StaticFilter::execute($key, UnderscoreToCamelCase::class));
            if (method_exists($this, $method)) {
                $this->{$method}($value);
            }
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getArrayCopy(): array
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
            'created'         => $this->formatCreated(\DateTime::ISO8601),
            'updated'         => $this->formatUpdated(\DateTime::ISO8601),
            'deleted'         => $this->formatDeleted(\DateTime::ISO8601),
            'parent_id'       => $this->getParentId(),
            'network_id'      => $this->getNetworkId(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getGroupId(): string
    {
        if ($this->groupId === null) {
            $this->setGroupId(Uuid::uuid1());
        }

        return $this->groupId;
    }

    /**
     * @inheritdoc
     */
    public function setGroupId(string $groupId): GroupInterface
    {
        $this->groupId = (string)$groupId;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getOrganizationId(): string
    {
        return $this->organizationId;
    }

    /**
     * @inheritdoc
     */
    public function setOrganizationId(string $organizationId): GroupInterface
    {
        $this->organizationId = $organizationId;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function attachToOrganization(OrganizationInterface $organization): GroupInterface
    {
        return $this->setOrganizationId($organization->getOrgId());
    }

    /**
     * @inheritdoc
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @inheritdoc
     */
    public function setTitle(string $title): GroupInterface
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @inheritdoc
     */
    public function setDescription(string $description = null): GroupInterface
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getHead(): int
    {
        return $this->head;
    }

    /**
     * @inheritdoc
     */
    public function setHead(int $head): GroupInterface
    {
        $this->head = abs($head);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTail(): int
    {
        return $this->tail;
    }

    /**
     * @inheritdoc
     */
    public function setTail(int $tail): GroupInterface
    {
        $this->tail = abs($tail);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDepth(): int
    {
        return $this->depth;
    }

    /**
     * @inheritdoc
     */
    public function setDepth(int $depth): GroupInterface
    {
        $this->depth = abs($depth);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isRoot(): bool
    {
        return $this->head === 1;
    }

    /**
     * @inheritdoc
     */
    public function hasChildren(): bool
    {
        if ($this->head === 0 || $this->tail === 0) {
            return false;
        }

        return $this->head !== ($this->tail - 1);
    }

    /**
     * @inheritdoc
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * @inheritdoc
     */
    public function setExternalId(string $externalId = null): GroupInterface
    {
        $this->externalId = $externalId;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @inheritdoc
     */
    public function setParentId(string $parentId = null): GroupInterface
    {
        $this->parentId = $parentId;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getNetworkId(): string
    {
        if (empty($this->networkId)) {
            $this->setNetworkId(Uuid::uuid1());
        }

        return $this->networkId;
    }

    /**
     * @inheritDoc
     */
    public function setNetworkId(string $networkId): GroupInterface
    {
        $this->networkId = (string)$networkId;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function attachToGroup(GroupInterface $parent): GroupInterface
    {
        return $this->setNetworkId($parent->getNetworkId())
            ->setParentId($parent->getGroupId());
    }

    /**
     * @inheritDoc
     */
    public function getDocumentType(): string
    {
        return 'group';
    }

    /**
     * @inheritDoc
     */
    public function getDocumentId(): string
    {
        return $this->getGroupId();
    }
}
