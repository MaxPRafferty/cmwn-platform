<?php

namespace Api\V1\Rest\User;

use Zend\Paginator\Paginator;

/**
 * A Collection of Users from the API
 *
 * @SWG\Definition(
 *     description="A Collection of Users from the API",
 *     @SWG\Property(
 *         type="object",
 *         property="_embedded",
 *         description="A List of User Entities",
 *         @SWG\Property(
 *             type="array",
 *             property="user",
 *             description="A List of users",
 *             @SWG\Items(ref="#/definitions/UserEntity")
 *         )
 *     ),
 *     allOf={
 *         @SWG\Schema(ref="#/definitions/Pagination")
 *     }
 * )
 */
class UserCollection extends Paginator
{
}
