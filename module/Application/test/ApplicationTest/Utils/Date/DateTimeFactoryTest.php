<?php

namespace ApplicationTest\Utils\Date;

use Application\Utils\Date\DateTimeFactory;
use PHPUnit\Framework\TestCase;

/**
 * Class DateTimeFactoryTest
 * @group Application
 * @group Utils
 * @group Date
 */
class DateTimeFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function testItShouldCreateDateTimeFromDateString()
    {
        $expectedDate = new \DateTimeImmutable();
        $testDate     = DateTimeFactory::factory($expectedDate->format(\DateTime::ISO8601));

        $this->assertEquals($expectedDate->format("Y-m-d H:i:s"), $testDate->format("Y-m-d H:i:s"));
    }

    /**
     * @test
     */
    public function testItShouldCreateDateTimeFromTimeStamp()
    {
        $expectedDate = new \DateTimeImmutable('1982-05-13 23:43:00');
        $testDate     = DateTimeFactory::factory(390181380);

        $this->assertEquals($expectedDate->format("Y-m-d H:i:s"), $testDate->format("Y-m-d H:i:s"));
    }

    /**
     * @test
     */
    public function testItShouldChangeTimeZoneToBeUTC()
    {
        $expectedDate = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $newDate      = new \DateTimeImmutable('now', new \DateTimeZone('America/New_York'));
        $testDate     = DateTimeFactory::factory($newDate->format(\DateTime::ISO8601));

        $this->assertEquals($expectedDate->format("Y-m-d H:i:s"), $testDate->format("Y-m-d H:i:s"));
    }

    /**
     * @test
     */
    public function testItShouldReturnNullWhenPassedNull()
    {
        $this->assertNull(DateTimeFactory::factory(null));
    }
}
