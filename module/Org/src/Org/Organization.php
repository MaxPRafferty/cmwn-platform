<?php

namespace Org;

use Application\Utils\Date\SoftDeleteTrait;
use Application\Utils\Date\StandardDatesTrait;
use Application\Utils\Meta\MetaDataTrait;
use Application\Utils\PropertiesTrait;
use Application\Utils\Type\TypeTrait;
use Ramsey\Uuid\Uuid;
use Zend\Filter\StaticFilter;
use Zend\Filter\Word\UnderscoreToCamelCase;

/**
 * An Organization
 */
class Organization implements OrganizationInterface
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
    protected $orgId;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * Organization constructor.
     * @param array|null $options
     */
    public function __construct(array $options = null)
    {
        if (is_array($options)) {
            $this->exchangeArray($options);
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getOrgId();
    }

    /**
     * @inheritdoc
     */
    public function exchangeArray(array $array): OrganizationInterface
    {
        $defaults = [
            'org_id'      => null,
            'title'       => null,
            'type'        => null,
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
            'org_id'      => $this->getOrgId(),
            'title'       => $this->getTitle(),
            'description' => $this->getDescription(),
            'type'        => $this->getType(),
            'meta'        => $this->getMeta(),
            'created'     => $this->formatCreated(\DateTime::ISO8601),
            'updated'     => $this->formatUpdated(\DateTime::ISO8601),
            'deleted'     => $this->formatDeleted(\DateTime::ISO8601),
        ];
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
    public function setDescription(string $description = null): OrganizationInterface
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrgId(): string
    {
        if ($this->orgId === null) {
            $this->setOrgId(Uuid::uuid1());
        }

        return $this->orgId;
    }

    /**
     * @inheritdoc
     */
    public function setOrgId(string $orgId): OrganizationInterface
    {
        $this->orgId = $orgId;

        return $this;
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
    public function setTitle(string $title): OrganizationInterface
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDocumentType(): string
    {
        return 'org';
    }

    /**
     * @inheritDoc
     */
    public function getDocumentId(): string
    {
        return $this->getOrgId();
    }
}
