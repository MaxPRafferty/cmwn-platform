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
    public function getAddressId();

    /**
     * @param string $addressId
     */
    public function setAddressId($addressId);

    /**
     * @return string
     */
    public function getAdministrativeArea();

    /**
     * @param string $administrativeArea
     */
    public function setAdministrativeArea($administrativeArea);
    /**
     * @return string
     */
    public function getSubAdministrativeArea();

    /**
     * @param string $subAdministrativeArea
     */
    public function setSubAdministrativeArea($subAdministrativeArea);

    /**
     * @return string
     */
    public function getLocality();

    /**
     * @param string $locality
     */
    public function setLocality($locality);

    /**
     * @return string
     */
    public function getDependentLocality();

    /**
     * @param string $dependentLocality
     */
    public function setDependentLocality($dependentLocality);

    /**
     * @return string
     */
    public function getPostalCode();

    /**
     * @param string $postalCode
     */
    public function setPostalCode($postalCode);

    /**
     * @return string
     */
    public function getThoroughfare();

    /**
     * @param string $thoroughfare
     */
    public function setThoroughfare($thoroughfare);

    /**
     * @return string
     */
    public function getPremise();

    /**
     * @param string $premise
     */
    public function setPremise($premise);
}
