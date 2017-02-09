<?php

namespace Address;

use Zend\Stdlib\ArraySerializableInterface;

/**
 * Interface AddressInterface
 * @package Address
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
