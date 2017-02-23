<?php

namespace AddressTest;

use Address\Address;
use Address\AddressInterface;
use PHPUnit\Framework\TestCase as TestCase;

/**
 * Class AddressTest
 * @package AddressTest
 */
class AddressTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldHydrateCorrectly()
    {
        $data = [
            'address_id'              => 'foo',
            'administrative_area'     => 'ny',
            'sub_administrative_area' => null,
            'locality'                => 'ny',
            'dependent_locality'      => null,
            'postal_code'             => '10036',
            'thoroughfare'            => '21 W 46th St',
            'premise'                 => null,
        ];

        $address = new Address([]);
        $address->exchangeArray($data);

        $this->compareFields($address, $data);
    }

    /**
     * @test
     */
    public function testItShouldExtractCorrectly()
    {
        $address = new Address([
            'address_id'              => 'foo',
            'administrative_area'     => 'ny',
            'sub_administrative_area' => null,
            'locality'                => 'ny',
            'dependent_locality'      => null,
            'postal_code'             => '10036',
            'thoroughfare'            => '21 W 46th St',
            'premise'                 => null,
        ]);

        $data = $address->getArrayCopy();

        $this->compareFields($address, $data);
    }

    /**
     * @param AddressInterface $address
     * @param array $data
     */
    protected function compareFields(AddressInterface $address, array $data)
    {
        $this->assertEquals($address->getAddressId(), $data['address_id']);
        $this->assertEquals($address->getAdministrativeArea(), $data['administrative_area']);
        $this->assertEquals($address->getSubAdministrativeArea(), $data['sub_administrative_area']);
        $this->assertEquals($address->getLocality(), $data['locality']);
        $this->assertEquals($address->getDependentLocality(), $data['dependent_locality']);
        $this->assertEquals($address->getPostalCode(), $data['postal_code']);
        $this->assertEquals($address->getThoroughfare(), $data['thoroughfare']);
        $this->assertEquals($address->getPremise(), $data['premise']);
    }
}
