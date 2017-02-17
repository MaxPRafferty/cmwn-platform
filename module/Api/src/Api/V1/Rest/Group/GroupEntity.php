<?php
namespace Api\V1\Rest\Group;

use Api\ScopeAwareInterface;
use Group\Group;
use Group\GroupInterface;
use Org\OrganizationInterface;

/**
 * A Group Entity represents the group through the API
 *
 * @SWG\Definition(
 *     description="A Group Entity represents the group through the API",
 *     @SWG\Property(
 *         type="object",
 *         property="_links",
 *         description="Links the group might have",
 *         allOf={
 *             @SWG\Schema(ref="#/definitions/SelfLink"),
 *         }
 *     ),
 *     @SWG\Property(
 *          type="integer",
 *          property="scope",
 *          description="Binary bits the authenticated user has access for this entity"
 *     ),
 *     allOf={
 *         @SWG\Schema(ref="#/definitions/Group"),
 *         @SWG\Schema(ref="#/definitions/SelfLink"),
 *     }
 * )
 */
class GroupEntity extends Group implements GroupInterface, ScopeAwareInterface
{
    /**
     * @var array
     */
    protected $organization;

    /**
     * @var array
     */
    protected $parent;

    /**
     * @inheritdoc
     */
    public function attachToOrganization(OrganizationInterface $organization): GroupInterface
    {
        $this->organization = $organization;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function attachToGroup(GroupInterface $parent): GroupInterface
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getArrayCopy(): array
    {
        $array = parent::getArrayCopy();
        unset($array['left']);
        unset($array['right']);
        unset($array['depth']);

        $array['organization'] = $this->organization;
        $array['parent']       = $this->parent;

        return $array;
    }

    /**
     * @inheritdoc
     */
    public function getEntityType()
    {
        return 'group' . (!empty($this->getType()) ? '.' . $this->getType() : '');
    }
}
