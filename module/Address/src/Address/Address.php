<?php

namespace Address;

use Zend\Filter\StaticFilter;
use Zend\Filter\Word\UnderscoreToCamelCase;
use Zend\Stdlib\ArraySerializableInterface;

/**
 * Class Address
 */
class Address implements AddressInterface
{
    /**
     * @var string
     */
    protected $addressId;

    /**
     * @var string
     */
    protected $administrativeArea;

    /**
     * @var string
     */
    protected $subAdministrativeArea;

    /**
     * @var string
     */
    protected $locality;

    /**
     * @var string
     */
    protected $dependentLocality;

    /**
     * @var string
     */
    protected $postalCode;

    /**
     * @var string
     */
    protected $thoroughfare;

    /**
     * @var string
     */
    protected $premise;

    /**
     * Address constructor.
     * @param array $array
     */
    public function __construct(array $array)
    {
        $this->exchangeArray($array);
    }

    /**
     * @inheritdoc
     */
    public function exchangeArray(array $array)
    {
        $defaults = [
            'address_id'              => null,
            'administrative_area'     => null,
            'sub_administrative_area' => null,
            'locality'                => null,
            'dependent_locality'      => null,
            'postal_code'             => null,
            'thoroughfare'            => null,
            'premise'                 => null,
        ];

        $array = array_merge($defaults, $array);

        foreach ($array as $key => $value) {
            $method = 'set' . ucfirst(StaticFilter::execute($key, UnderscoreToCamelCase::class));
            if (method_exists($this, $method)) {
                $this->{$method}($value);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function getArrayCopy()
    {
        return [
            'address_id'              => $this->getAddressId(),
            'administrative_area'     => $this->getAdministrativeArea(),
            'sub_administrative_area' => $this->getSubAdministrativeArea(),
            'locality'                => $this->getLocality(),
            'dependent_locality'      => $this->getDependentLocality(),
            'postal_code'             => $this->getPostalCode(),
            'thoroughfare'            => $this->getThoroughfare(),
            'premise'                 => $this->getPremise(),
        ];
    }

    /**
     * @return string
     */
    public function getAddressId()
    {
        return $this->addressId;
    }

    /**
     * @param string $addressId
     */
    public function setAddressId($addressId)
    {
        $this->addressId = $addressId;
    }

    /**
     * @return string
     */
    public function getAdministrativeArea()
    {
        return $this->administrativeArea;
    }

    /**
     * @param string $administrativeArea
     */
    public function setAdministrativeArea($administrativeArea)
    {
        $this->administrativeArea = $administrativeArea;
    }

    /**
     * @return string
     */
    public function getSubAdministrativeArea()
    {
        return $this->subAdministrativeArea;
    }

    /**
     * @param string $subAdministrativeArea
     */
    public function setSubAdministrativeArea($subAdministrativeArea)
    {
        $this->subAdministrativeArea = $subAdministrativeArea;
    }

    /**
     * @return string
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * @param string $locality
     */
    public function setLocality($locality)
    {
        $this->locality = $locality;
    }

    /**
     * @return string
     */
    public function getDependentLocality()
    {
        return $this->dependentLocality;
    }

    /**
     * @param string $dependentLocality
     */
    public function setDependentLocality($dependentLocality)
    {
        $this->dependentLocality = $dependentLocality;
    }

    /**
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @param string $postalCode
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
    }

    /**
     * @return string
     */
    public function getThoroughfare()
    {
        return $this->thoroughfare;
    }

    /**
     * @param string $thoroughfare
     */
    public function setThoroughfare($thoroughfare)
    {
        $this->thoroughfare = $thoroughfare;
    }

    /**
     * @return string
     */
    public function getPremise()
    {
        return $this->premise;
    }

    /**
     * @param string $premise
     */
    public function setPremise($premise)
    {
        $this->premise = $premise;
    }
}
