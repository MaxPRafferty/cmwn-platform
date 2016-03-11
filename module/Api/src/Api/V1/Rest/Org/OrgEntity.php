<?php

namespace Api\V1\Rest\Org;

use Org\Organization;
use Org\OrganizationInterface;
use Zend\Stdlib\ArrayUtils;

/**
 * Class OrgEntity
 * @package Api\V1\Rest\Org
 */
class OrgEntity extends Organization
{
    public function __construct($options)
    {
        if ($options instanceof OrganizationInterface) {
            $options = $options->getArrayCopy();
        }

        if ($options instanceof \Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        parent::__construct($options);
    }
}
