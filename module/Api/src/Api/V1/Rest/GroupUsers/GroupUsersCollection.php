<?php

namespace Api\V1\Rest\GroupUsers;

use Zend\Paginator\Paginator;

/**
 * A Collection of Users of the group from the API
 *
 * @SWG\Definition(
 *     description="A Collection of Users of the group from the API",
 *     @SWG\Property(
 *         type="object",
 *         property="_embedded",
 *         description="A List of User Entities",
 *         @SWG\Property(
 *             type="array",
 *             property="items",
 *             description="A List of users in the group",
 *             @SWG\Items(ref="#/definitions/UserEntity")
 *         )
 *     ),
 *     allOf={
 *         @SWG\Schema(ref="#/definitions/Pagination")
 *     }
 * )
 */
class GroupUsersCollection extends Paginator
{
}
