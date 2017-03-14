<?php

namespace Api\V1\Rest\Org;

use Zend\Paginator\Paginator;

/**
 * A Collection of Organizations from the API
 *
 * @SWG\Definition(
 *     description="A Collection of Organizations from the API",
 *     @SWG\Property(
 *         type="object",
 *         property="_embedded",
 *         description="A List of Organizations Entities",
 *         @SWG\Property(
 *             type="array",
 *             property="org",
 *             description="A List of organizations",
 *             @SWG\Items(ref="#/definitions/OrgEntity")
 *         )
 *     ),
 *     allOf={
 *         @SWG\Schema(ref="#/definitions/Pagination")
 *     }
 * )
 */
class OrgCollection extends Paginator
{
}
