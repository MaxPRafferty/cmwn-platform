<?php

namespace Api\V1\Rest\Org;

use Api\ScopeAwareInterface;
use Org\Organization;
use Org\OrganizationInterface;
use Zend\Stdlib\ArrayUtils;

/**
 * Class OrgEntity
 * @package Api\V1\Rest\Org
 */
class OrgEntity extends Organization implements ScopeAwareInterface
{
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
     * Gets the entity type to allow the rbac to set the correct scope
     *
     * @return string
     */
    public function getEntityType()
    {
        return 'organization';
    }


}
