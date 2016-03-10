<?php

namespace Api\V1\Rest\Org;

use Org\Organization;
use Org\OrganizationInterface;
use ZF\Hal\Entity;

/**
 * Class OrgEntity
 * @package Api\V1\Rest\Org
 */
class OrgEntity extends Entity
{
    /**
     * @var null
     */
    protected $scope;

    /**
     * OrgEntity constructor.
     *
     * @param OrganizationInterface $entity
     */
    public function __construct($entity = null, $scope = null)
    {
        $entity = $entity instanceof OrganizationInterface
            ? $entity->getArrayCopy()
            : $entity;

        parent::__construct($entity);
        $this->scope = (int)$scope;
    }

    /**
     * @return array
     */
    public function getArrayCopy()
    {
        if (is_array($this->entity)) {
            return $this->entity;
        }

        return [];
    }
}
