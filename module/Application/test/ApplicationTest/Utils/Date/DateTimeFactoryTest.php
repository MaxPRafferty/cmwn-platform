<?php


namespace ApplicationTest\Utils\Date;


use Application\Utils\Date\DateTimeFactory;

class DateTimeFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testItShouldCreateDateTimeFromDateString()
    {
        $expectedDate = new \DateTimeImmutable();
        $testDate     = DateTimeFactory::factory($expectedDate->format(\DateTime::ISO8601));

        $this->assertEquals($expectedDate->getTimestamp(), $testDate->getTimestamp());
    }

    public function testItShouldCreateDateTimeFromTimeStamp()
    {
        $expectedDate = new \DateTimeImmutable('1982-05-13 23:43:00');
        $testDate     = DateTimeFactory::factory(390181380);

        $this->assertEquals($expectedDate->getTimestamp(), $testDate->getTimestamp());
    }

    public function testItShouldChangeTimeZoneToBeUTC()
    {
        $expectedDate = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $newDate      = new \DateTimeImmutable('now', new \DateTimeZone('America/New_York'));
        $testDate     = DateTimeFactory::factory($newDate->getTimestamp());

        $this->assertEquals($expectedDate->getTimestamp(), $testDate->getTimestamp());
    }

    public function testItShouldReturnNullWhenPassedNull()
    {
        $this->assertNull(DateTimeFactory::factory(null));
    }
}
