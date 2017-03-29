<?php

namespace Api\V1\Rest\Org;

use Api\ScopeAwareInterface;
use Org\Organization;

/**
 * An Org Entity represents the organization though the API
 *
 * @SWG\Definition(
 *     description="An Org Entity represents the organization though the API",
 *     @SWG\Property(
 *         type="object",
 *         property="_links",
 *         description="Links the organization might have",
 *         allOf={
 *             @SWG\Schema(ref="#/definitions/SelfLink"),
 *         }
 *     ),
 *     allOf={
 *         @SWG\Schema(ref="#/definitions/Organization"),
 *         @SWG\Schema(ref="#/definitions/SelfLink"),
 *     }
 * )
 */
class OrgEntity extends Organization implements ScopeAwareInterface
{

    /**
     * @inheritdoc
     */
    public function getEntityType()
    {
        return 'organization' . (!empty($this->getType()) ? '.' . $this->getType() : '');
    }
}
