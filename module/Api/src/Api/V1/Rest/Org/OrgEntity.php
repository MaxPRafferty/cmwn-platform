<?php

namespace Api\V1\Rest\Org;

use Api\ScopeAwareInterface;
use Org\Organization;
use Org\OrganizationInterface;
use Zend\Stdlib\ArrayUtils;
use ZF\Hal\Link\LinkCollection;
use ZF\Hal\Link\LinkCollectionAwareInterface;

/**
 * Class OrgEntity
 * @package Api\V1\Rest\Org
 */
class OrgEntity extends Organization implements ScopeAwareInterface, LinkCollectionAwareInterface
{
    /**
     * @var LinkCollection
     */
    protected $links;

    /**
     * OrgEntity constructor.
     * @param array $options
     */
    public function __construct($options = [])
    {
        if ($options instanceof OrganizationInterface) {
            $options = $options->getArrayCopy();
        }

        if ($options instanceof \Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        parent::__construct($options);
    }

    /**
     * @return mixed
     */
    public function getArrayCopy()
    {
        return array_merge(
            parent::getArrayCopy(),
            ['links' => $this->getLinks()]
        );
    }

    /**
     * Gets the entity type to allow the rbac to set the correct scope
     *
     * @return string
     */
    public function getEntityType()
    {
        return 'organization' . (!empty($this->getType()) ? '.' . $this->getType() : '');
    }

    /**
     * @param LinkCollection $links
     */
    public function setLinks(LinkCollection $links)
    {
        $this->links = $links;
    }

    /**
     * @return LinkCollection
     */
    public function getLinks()
    {
        if ($this->links === null) {
            $this->setLinks(new LinkCollection());
        }

        return $this->links;
    }
}
