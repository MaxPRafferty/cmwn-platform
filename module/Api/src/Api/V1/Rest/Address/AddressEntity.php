<?php

namespace Api\V1\Rest\Address;

use Address\Address;
use Address\AddressInterface;

/**
 * A Address Entity represents the address through the API
 *
 * @SWG\Definition(
 *     description="A Address Entity represents the address through the API",
 *     @SWG\Property(
 *         type="object",
 *         property="_links",
 *         description="Links the address might have",
 *         allOf={
 *             @SWG\Schema(ref="#/definitions/SelfLink"),
 *         }
 *     ),
 *     allOf={
 *         @SWG\Schema(ref="#/definitions/Address"),
 *         @SWG\Schema(ref="#/definitions/SelfLink"),
 *     }
 * )
 */
class AddressEntity extends Address implements AddressInterface
{
}
