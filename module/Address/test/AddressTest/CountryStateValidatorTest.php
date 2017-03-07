<?php

namespace AddressTest;

use Address\CountryStateValidator;
use PHPUnit\Framework\TestCase;

/**
 * unit tests for address validator
 */
class CountryStateValidatorTest extends TestCase
{
    /**
     * @var CountryStateValidator
     */
    protected $validator;

    /**
     * @test
     */
    public function testItShouldValidateExistingCountry()
    {
        $this->validator = new CountryStateValidator(['fieldName' => 'country']);
        $this->assertTrue($this->validator->isValid('US'));
    }

    /**
     * @test
     */
    public function testItShouldNotValidateNonExistingCountry()
    {
        $this->validator = new CountryStateValidator(['fieldName' => 'country']);
        $this->assertFalse($this->validator->isValid('FOO_BAR'));
    }

    /**
     * @test
     */
    public function testItShouldValidateExistingState()
    {
        $this->validator = new CountryStateValidator(['fieldName' => 'administrative_area']);
        $this->assertTrue($this->validator->isValid('US-NY', ['country' => 'US']));
    }

    /**
     * @test
     */
    public function testItShouldNotValidateNonExistingState()
    {
        $this->validator = new CountryStateValidator(['fieldName' => 'administrative_area']);
        $this->assertFalse($this->validator->isValid('FOO_BAR', ['country' => 'US']));
    }
}
