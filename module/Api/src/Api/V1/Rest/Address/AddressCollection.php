<?php

namespace Api\V1\Rest\Address;

use Zend\Paginator\Paginator;

/**
 * A Collection of Addresses from the API
 *
 * @SWG\Definition(
 *     description="A Collection of Addresses from the API",
 *     @SWG\Property(
 *         type="object",
 *         property="_embedded",
 *         description="A List of Address Entities",
 *         @SWG\Property(
 *             type="array",
 *             property="address",
 *             description="A List of Addresses",
 *             @SWG\Items(ref="#/definitions/AddressEntity")
 *         )
 *     ),
 *     allOf={
 *         @SWG\Schema(ref="#/definitions/Pagination")
 *     }
 * )
 */
class AddressCollection extends Paginator
{
}
