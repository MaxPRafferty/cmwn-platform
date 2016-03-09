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
        if ($entity instanceof OrganizationInterface) {
            parent::__construct($entity);
            $this->scope = (int)$scope;
        }
    }

    public function exchangeArray(array $data)
    {
        parent::__construct(new Organization($data));
    }

    public function getArrayCopy()
    {
        $data = $this->entity->getArrayCopy();
        if (null !== $this->scope) {
            $data['scope'] = $this->scope;
        }

        return $data;
    }
}
