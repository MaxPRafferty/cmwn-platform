<?php

namespace MediaTest;

use Media\MediaProperties;
use \PHPUnit_Framework_TestCase as TestCase;

/**
 * Test MediaPropertiesTest
 *
 * @group Media
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class MediaPropertiesTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldSetAllowedProperties()
    {
        $properties = new MediaProperties();
        $properties->setProperty(MediaProperties::CAN_OVERLAP, true);
        $this->assertTrue($properties->getProperty(MediaProperties::CAN_OVERLAP));
    }

    /**
     * @test
     */
    public function testItShouldAllowAccessToPropertiesFromObject()
    {
        $properties = new MediaProperties();
        $properties->setProperty(MediaProperties::CAN_OVERLAP, true);
        $this->assertTrue($properties->can_overlap);
    }

    /**
     * @test
     */
    public function testItShouldAllowSettingOfPropertiesFromObject()
    {
        $properties = new MediaProperties();
        $properties->can_overlap = true;
        $this->assertTrue($properties->can_overlap);
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenTryingToSetInvalidProperty()
    {
        $this->setExpectedException(
            \OutOfRangeException::class,
            'Invalid property: "foo_bar"'
        );

        $properties = new MediaProperties();
        $properties->setProperty('foo_bar', 'bax bat');
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenTryingToGetInvalidProperty()
    {
        $this->setExpectedException(
            \OutOfRangeException::class,
            'Cannot access property: "foo_bar'
        );

        $properties = new MediaProperties();
        $properties->getProperty('foo_bar');
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenTryingToSetInvalidPropertyFromObject()
    {
        $this->setExpectedException(
            \OutOfRangeException::class,
            'Invalid property: "foo_bar"'
        );

        $properties = new MediaProperties();
        $properties->foo_bar = 'bax bat';
    }

    /**
     * @test
     */
    public function testItShouldThrowExceptionWhenTryingToGetInvalidPropertyFromObject()
    {
        $this->setExpectedException(
            \OutOfRangeException::class,
            'Cannot access property: "foo_bar'
        );

        $properties = new MediaProperties();
        $properties->foo_bar;
    }
}
