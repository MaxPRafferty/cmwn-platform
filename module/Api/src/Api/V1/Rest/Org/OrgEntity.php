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
    /**
     * @var int
     */
    protected $scope = 0;

    public function __construct($options, $scope = null)
    {
        if ($options instanceof OrganizationInterface) {
            $options = $options->getArrayCopy();
        }

        if ($options instanceof \Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if ($scope !== null) {
            $this->scope = $scope;
        }
        parent::__construct($options);
    }

    public function getArrayCopy()
    {
        return array_merge(
            parent::getArrayCopy(),
            ['scope' => $this->scope]
        );
    }
}
