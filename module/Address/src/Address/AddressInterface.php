<?php

namespace Address;

use Zend\Stdlib\ArraySerializableInterface;

/**
 * Address
 *
 * geographical address of a group
 *
 * @SWG\Definition(
 *     definition="Address",
 *     description="Address is a geographical address of a group",
 *     required={"administrative_area","locality","postal_code","thoroughfare"},
 *     @SWG\Property(
 *          type="string",
 *          format="uuid",
 *          property="address_id",
 *          description="The id of the address"
 *     ),
 *     @SWG\Property(
 *          type="string",
 *          property="administrative_area",
 *          description="state/province/region"
 *     ),
 *     @SWG\Property(
 *          type="string",
 *          property="sub_administrative_area",
 *          description="County/District"
 *     ),
 *     @SWG\Property(
 *          type="string",
 *          property="locality",
 *          description="city/town"
 *     ),
 *     @SWG\Property(
 *          type="string",
 *          property="dependent_locality",
 *          description="dependent locality"
 *     ),
 *     @SWG\Property(
 *          type="string",
 *          property="postal_code",
 *          description="postal code/ zip code"
 *     ),
 *     @SWG\Property(
 *          type="string",
 *          property="thoroughfare",
 *          description="street address"
 *     ),
 *     @SWG\Property(
 *          type="string",
 *          property="premise",
 *          description="Apartment, Suite, Box number, etc'"
 *     ),
 * )
 */
interface AddressInterface extends ArraySerializableInterface
{
    /**
     * @return string
     */
    public function getAddressId() : string;

    /**
     * @param string $addressId
     */
    public function setAddressId(string $addressId = null);

    /**
     * @return string
     */
    public function getAdministrativeArea() : string;

    /**
     * @param string $administrativeArea
     */
    public function setAdministrativeArea(string $administrativeArea = null);
    /**
     * @return string
     */
    public function getSubAdministrativeArea() : string;

    /**
     * @param string $subAdministrativeArea
     */
    public function setSubAdministrativeArea(string $subAdministrativeArea = null);

    /**
     * @return string
     */
    public function getLocality() : string;

    /**
     * @param string $locality
     */
    public function setLocality(string $locality = null);

    /**
     * @return string
     */
    public function getDependentLocality() : string;

    /**
     * @param string $dependentLocality
     */
    public function setDependentLocality(string $dependentLocality = null);

    /**
     * @return string
     */
    public function getPostalCode() : string;

    /**
     * @param string $postalCode
     */
    public function setPostalCode(string $postalCode = null);

    /**
     * @return string
     */
    public function getThoroughfare() : string;

    /**
     * @param string $thoroughfare
     */
    public function setThoroughfare(string $thoroughfare = null);

    /**
     * @return string
     */
    public function getPremise() : string;

    /**
     * @param string $premise
     */
    public function setPremise(string $premise = null);
}
