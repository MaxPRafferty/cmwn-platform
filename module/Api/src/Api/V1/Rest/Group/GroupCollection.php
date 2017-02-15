<?php

namespace Api\V1\Rest\Group;

use Zend\Paginator\Paginator;

/**
 * A Collection of Groups from the API
 *
 * @SWG\Definition(
 *     description="A Collection of Groups from the API",
 *     @SWG\Property(
 *         type="object",
 *         property="_embedded",
 *         description="A List of Group Entities",
 *         @SWG\Property(
 *             type="array",
 *             property="group",
 *             description="A List of groups",
 *             @SWG\Items(ref="#/definitions/GroupEntity")
 *         )
 *     ),
 *     allOf={
 *         @SWG\Schema(ref="#/definitions/Pagination")
 *     }
 * )
 */
class GroupCollection extends Paginator
{
}
