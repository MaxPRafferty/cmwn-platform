<?php

namespace Org;

use Application\Utils\Date\DateCreatedTrait;
use Application\Utils\Date\DateDeletedTrait;
use Application\Utils\Date\DateUpdatedTrait;
use Application\Utils\MetaDataTrait;
use Application\Utils\PropertiesTrait;
use Application\Utils\SoftDeleteInterface;
use Zend\Filter\StaticFilter;
use Zend\Stdlib\ArraySerializableInterface;

/**
 * Class Organization
 *
 * @package Org
 */
class Organization implements OrganizationInterface, ArraySerializableInterface, SoftDeleteInterface
{
    use DateUpdatedTrait;
    use DateCreatedTrait;
    use DateDeletedTrait;
    use MetaDataTrait;
    use PropertiesTrait;

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
     * @var string
     */
    protected $type;

    public function __construct(array $options = null)
    {
        if (is_array($options)) {
            $this->exchangeArray($options);
        }
    }

    /**
     * Converts an Array into something that can be digested here
     *
     * @param array $array
     */
    public function exchangeArray(array $array)
    {
        $defaults = [
            'org_id'      => null,
            'title'       => null,
            'description' => null,
            'type'        => null,
            'meta'        => [],
            'created'     => null,
            'updated'     => null,
            'deleted'     => null,
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
     * Return this object represented as an array
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return [
            'org_id'      => $this->getOrgId(),
            'title'       => $this->getTitle(),
            'description' => $this->getDescription(),
            'type'        => $this->getType(),
            'meta'        => $this->getMeta(),
            'created'     => $this->getCreated() !== null ? $this->getCreated()->format(\DateTime::ISO8601) : null,
            'updated'     => $this->getUpdated() !== null ? $this->getUpdated()->format(\DateTime::ISO8601) : null,
            'deleted'     => $this->getDeleted() !== null ? $this->getDeleted()->format(\DateTime::ISO8601) : null,
        ];
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
     * @return Organization
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrgId()
    {
        return $this->orgId;
    }

    /**
     * @param string $orgId
     * @return Organization
     */
    public function setOrgId($orgId)
    {
        $this->orgId = $orgId;

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
     * @return Organization
     */
    public function setTitle($title)
    {
        $this->title = $title;

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
     * @return Organization
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }
}
